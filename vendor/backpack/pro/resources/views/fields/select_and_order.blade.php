{{-- select_and_order --}}
@php
    $values = old_empty_or_null($field['name'], []) ??  $field['value'] ?? $field['default'] ?? [];
    $dragClass = str_replace('[', '-', str_replace(']', '-', $field['name'])) . '_connectedSortable';
    $values = (array)$values;
@endphp

@include('crud::fields.inc.wrapper_start')
    <label>{!! $field['label'] !!}</label>
    @include('crud::fields.inc.translatable_icon')
    <div class="row"
         data-init-function="bpFieldInitSelectAndOrderElement"
         data-all-options='@json($field['options'])'
         data-field-name="{{ $field['name'] }}"
         data-drag-class-name="{{ $dragClass }}">
        <div class="col-md-12 d-flex justify-content-between">
            <ul data-identifier="drag-destination" class="{{ $dragClass }} select_and_order_selected float-left"></ul>
            <ul data-identifier="drag-source" class="{{ $dragClass }} select_and_order_all float-right"></ul>

            {{-- The results will be stored here --}}
            <div data-identifier="results">
                <select class="d-none"
                    name="{{ $field['name'] }}[]"
                    data-selected-options='@json($values)'
                    bp-field-main-input
                    multiple>
                </select>
            </div>
        </div>

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
    </div>
@include('crud::fields.inc.wrapper_end')


{{-- ########################################## --}}
{{-- Extra CSS and JS for this particular field --}}
{{-- If a field type is shown multiple times on a form, the CSS and JS will only be loaded once --}}


{{-- FIELD CSS - will be loaded in the after_styles section --}}
@push('crud_fields_styles')
    @bassetBlock('backpack/pro/fields/select-and-order-field.css')
    <style>
        .select_and_order_all,
        .select_and_order_selected {
            min-height: 120px;
            list-style-type: none;
            max-height: 220px;
            overflow: scroll;
            overflow-x: hidden;
            padding: 0px 5px 5px 5px;
            border: 1px solid #e6e6e6;
            width: 48%;
        }
        .select_and_order_all {
            border: none;
        }
        .select_and_order_all li,
        .select_and_order_selected li{
            border: 1px solid #eee;
            margin-top: 5px;
            padding: 5px;
            font-size: 1em;
            overflow: hidden;
            cursor: grab;
            border-style: dashed;
        }
        .select_and_order_all li {
            background: #fbfbfb;
            color: grey;
        }
        .select_and_order_selected li {
            border-style: solid;
        }
        .select_and_order_all li.ui-sortable-helper,
        .select_and_order_selected li.ui-sortable-helper {
            color: #3c8dbc;
            border-collapse: #3c8dbc;
            z-index: 9999;
        }
        .select_and_order_all .ui-sortable-placeholder,
        .select_and_order_selected .ui-sortable-placeholder {
            border: 1px dashed #3c8dbc;
            visibility: visible!important;
        }
        /* Touch device */
        @media (hover: none) and (pointer: coarse) {
            .select_and_order_all {
                border: 1px solid #e6e6e6;
            }
            .select_and_order_all li,
            .select_and_order_selected li {
                width: 90%;
            }
        }
    </style>
    @endBassetBlock
@endpush

{{-- FIELD JS - will be loaded in the after_scripts section --}}
@push('crud_fields_scripts')
    @basset('https://cdn.jsdelivr.net/npm/jquery-ui@1.13.2/dist/jquery-ui.min.js')
    @basset('https://cdn.jsdelivr.net/npm/jquery-ui-touch-punch@0.2.3/jquery.ui.touch-punch.min.js')

    @bassetBlock('backpack/pro/fields/select-and-order-field.js')
    <script>
    function bpFieldInitSelectAndOrderElement(element) {
        var $dragSource = element.find('[data-identifier=drag-source]');
        var $dragDestination = element.find('[data-identifier=drag-destination]');
        var $hiddenSelect = element.find('[data-identifier=results] select');
        var $dragClassName = element.attr('data-drag-class-name');
        var $alreadySelectedOptions = $hiddenSelect.data('selected-options');
        var $allOptions = element.data('all-options');

        // selected options should be an array no matter what was received (string or direct array)
        // useful if the selected-options were set by the repeatable field
        if (typeof $alreadySelectedOptions === 'string' ) {
            $alreadySelectedOptions = $alreadySelectedOptions.split(",");
        }

        // set unique IDs on the drag-and-drop areas so we can reference them later on
        var $allId = 'sao_all_'+Math.ceil(Math.random() * 1000000);
        var $selectedId = 'sao_selected_'+Math.ceil(Math.random() * 1000000);

        element.find('[data-identifier=drag-destination]').attr('id', $selectedId);
        element.find('[data-identifier=drag-source]').attr('id', $allId);

        // initialize jQueryUI sortable
        $( "#"+$allId+", #"+$selectedId ).sortable({
            connectWith: "." + $dragClassName,
            create: function (event, ui) {
                // populate all options in the right-hand area (aka $dragSource)
                if (Object.keys($allOptions).length) {
                    $dragSource.html("");

                    for (value in $allOptions) {
                        $dragSource.append('<li value="'+value+'"><i class="la la-arrows"></i> '+$allOptions[value]+'</li>');
                    }
                }

                // populate selected options in the left-hand area (aka $dragDestination)
                if ($alreadySelectedOptions.length) {
                    if ($alreadySelectedOptions.length == 1 && ($alreadySelectedOptions[0] =='' || $alreadySelectedOptions == ' ' ) ) {
                        return;
                    }

                    $dragDestination.html("");
                    $hiddenSelect.html("");

                    $alreadySelectedOptions.forEach(function(value, key) {
                        $dragDestination.append('<li value="'+value+'"><i class="la la-arrows"></i> '+$allOptions[value]+'</li>');
                        $dragSource.find('li[value="'+value+'"]').remove();
                        $hiddenSelect.append('<option value="'+value+'" selected></option>');
                    });
                }
            },
            update: function() {
                var updatedlist = $(this).attr('id');

                if((updatedlist == $selectedId)) {
                    // clear all options inside the select
                    $hiddenSelect.html("");

                    // if there are no items dragged inside the selected area, abort
                    if($dragDestination.find('li').length=0) {
                        return;
                    }

                    // for each item dragged inside the selected area
                    // add a new selected option inside the hidden select
                    $dragDestination.find('li').each(function(val,obj) {
                        $hiddenSelect.append('<option value="'+obj.getAttribute('value')+'" selected></option>');
                    });
                    
                    $hiddenSelect.trigger('change');
                }
            }
        }).disableSelection();

        $hiddenSelect.on('CrudField:disable', function(e) {
            $( "#"+$allId+", #"+$selectedId ).sortable("option","disabled", true);        
        });

        $hiddenSelect.on('CrudField:enable', function(e) {
            $( "#"+$allId+", #"+$selectedId ).sortable("option","disabled", false);
        });
    }
    </script>
    @endBassetBlock
@endpush

{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}
