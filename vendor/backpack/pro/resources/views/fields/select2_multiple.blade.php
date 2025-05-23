{{-- select2 multiple --}}
@php
    if (!isset($field['options'])) {
        $field['options'] = $field['model']::all();
    } else {
        $field['options'] = call_user_func($field['options'], $field['model']::query());
    }

    //build option keys array to use with Select All in javascript.
    $model_instance = new $field['model'];
    $options_ids_array = $field['options']->pluck($model_instance->getKeyName())->toArray();

    $field['multiple'] = true;
    $field['allows_null'] = $field['allows_null'] ?? $crud->model::isColumnNullable($field['name']);
    $field['placeholder'] = $field['placeholder'] ?? trans('backpack::crud.select_entries');
    $field['closeOnSelect'] ??= false;

    $field['value'] = old_empty_or_null($field['name'], collect()) ??  $field['value'] ?? $field['default'] ?? collect();
    
    // when value is returned from crud panel it will be a collection, when returns from `old()` is already an array
    if (is_a($field['value'], \Illuminate\Support\Collection::class)) {
        $field['value'] = $field['value']->pluck($model_instance->getKeyName())->toArray();
    }
@endphp

@include('crud::fields.inc.wrapper_start')
    <label>{!! $field['label'] !!}</label>
    @include('crud::fields.inc.translatable_icon')
    {{-- To make sure a value gets submitted even if the "select multiple" is empty, we need a hidden input --}}
    <input type="hidden" name="{{ $field['name'] }}" value="" @if(in_array('disabled', $field['attributes'] ?? [])) disabled @endif />
    <select
        name="{{ $field['name'] }}[]"
        style="width: 100%"
        data-init-function="bpFieldInitSelect2MultipleElement"
        data-field-is-inline="{{var_export($inlineCreate ?? false)}}"
        data-select-all="{{ var_export($field['select_all'] ?? false)}}"
        data-options-for-js="{{json_encode(array_values($options_ids_array))}}"
        data-language="{{ str_replace('_', '-', app()->getLocale()) }}"
        data-allows-null="{{var_export($field['allows_null'])}}"
        data-placeholder="{{$field['placeholder']}}"
        data-close-on-select="{{var_export($field['closeOnSelect'])}}"
        bp-field-main-input
        @include('crud::fields.inc.attributes', ['default_class' =>  'form-control select2_multiple'])
        multiple>

        @if (isset($field['model']))
            @foreach ($field['options'] as $option)
                @if(in_array($option->getKey(), $field['value']))
                    <option value="{{ $option->getKey() }}" selected>{{ $option->{$field['attribute']} }}</option>
                @else
                    <option value="{{ $option->getKey() }}">{{ $option->{$field['attribute']} }}</option>
                @endif
            @endforeach
        @endif
    </select>

    @if(isset($field['select_all']) && $field['select_all'])
        <a class="btn btn-xs btn-default select_all" style="margin-top: 5px;"><i class="la la-check-square-o"></i> {{ trans('backpack::crud.select_all') }}</a>
        <a class="btn btn-xs btn-default clear" style="margin-top: 5px;"><i class="la la-times"></i> {{ trans('backpack::crud.clear') }}</a>
    @endif

    {{-- HINT --}}
    @if (isset($field['hint']))
        <p class="help-block">{!! $field['hint'] !!}</p>
    @endif
@include('crud::fields.inc.wrapper_end')


{{-- ########################################## --}}
{{-- Extra CSS and JS for this particular field --}}

    {{-- FIELD CSS - will be loaded in the after_styles section --}}
    @push('crud_fields_styles')
        {{-- include select2 css --}}
        @basset('https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css')
        @basset('https://cdn.jsdelivr.net/npm/select2-bootstrap-theme@0.1.0-beta.10/dist/select2-bootstrap.min.css')
    @endpush

    {{-- FIELD JS - will be loaded in the after_scripts section --}}
    @push('crud_fields_scripts')
        {{-- include select2 js --}}
        @basset('https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js')
        @if (app()->getLocale() !== 'en')
            @basset('https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/i18n/' . str_replace('_', '-', app()->getLocale()) . '.js')
        @endif
        @bassetBlock('backpack/pro/fields/select2-multiple-field.js')
        <script>
            function bpFieldInitSelect2MultipleElement(element) {

                var $select_all = element.attr('data-select-all');
                if (!element.hasClass("select2-hidden-accessible"))
                {
                    let $isFieldInline = element.data('field-is-inline');
                    let $allowClear = element.data('allows-null');
                    let $multiple = element.attr('multiple') ?? false;
                    let $placeholder = element.attr('placeholder');
                    let $closeOnSelect = element.data('close-on-select');

                    var $obj = element.select2({
                        theme: "bootstrap",
                        allowClear: $allowClear,
                        multiple: $multiple,
                        placeholder: $placeholder,
                        dropdownParent: $isFieldInline ? $('#inline-create-dialog .modal-content') : $(document.body),
                        closeOnSelect: $closeOnSelect

                    });

                    //get options ids stored in the field.
                    var options = JSON.parse(element.attr('data-options-for-js'));

                    if($select_all) {
                        element.parent().find('.clear').on("click", function () {
                            $obj.val([]).trigger("change");
                        });
                        element.parent().find('.select_all').on("click", function () {
                            $obj.val(options).trigger("change");
                        });
                    }
                }

                $(element).on('CrudField:disable', function(e) {
                    let hiddenInput = element.siblings('input[type="hidden"]');
                    if(hiddenInput.length) {
                        hiddenInput.prop('disabled',true);
                    }
                    return true;
                });

                $(element).on('CrudField:enable', function(e) { 
                    let hiddenInput = element.siblings('input[type="hidden"]');
                    if(hiddenInput.length) {
                        hiddenInput.prop('disabled',false);
                    }
                    return true;
                });
            }
        </script>
        @endBassetBlock
    @endpush

{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}
