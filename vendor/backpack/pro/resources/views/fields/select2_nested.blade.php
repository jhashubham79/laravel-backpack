{{-- select2_nested --}}

{{-- Thanks to Erwan Pianezza - https://github.com/breizhwave--}}

{{-- This field assumes you have a nested set Eloquent model, using: --}}
{{-- 1. children() as a properly defined relationship --}}
{{-- 2. depth, lft attributes --}}

@php
    $current_value = old_empty_or_null($field['name'], '') ??  $field['value'] ?? $field['default'] ?? '';
    if (!empty($current_value)) {
        $current_value = is_object($current_value) ? $current_value->getKey() : $current_value;
    }
    if (!function_exists('echoSelect2NestedEntry')) {
        function echoSelect2NestedEntry($entry, $field, $current_value) {
            if ($current_value == $entry->getKey()) {
                $selected = ' selected ';
            } else {
                $selected = '';
            }
            echo '<option value="'.$entry->getKey().'"'.$selected.'>';
            echo str_repeat("-", (int)$entry->depth > 1 ? (int)$entry->depth - 1 : 0).' '.$entry->{$field['attribute']};
            echo "</option>";
        }
    }

    if (!function_exists('echoSelect2NestedChildren')) {
        function echoSelect2NestedChildren($entity, $field, $current_value)
        {
         foreach ($entity->children as $entry)
            {
                echoSelect2NestedEntry($entry, $field, $current_value);
                echoSelect2NestedChildren($entry, $field, $current_value);
            }
        }
    }

    $entity_model = $crud->getRelationModel($field['entity'], -1);

    $field['allows_null'] = $field['allows_null'] ?? $entity_model::isColumnNullable($field['name']);
    $field['placeholder'] = $field['placeholder'] ?? trans('backpack::crud.select_entry');
@endphp

@include('crud::fields.inc.wrapper_start')
    <label>{!! $field['label'] !!}</label>
    @include('crud::fields.inc.translatable_icon')

    <select
        name="{{ $field['name'] }}"
        style="width: 100%"
        data-init-function="bpFieldInitSelect2NestedElement"
        data-field-is-inline="{{var_export($inlineCreate ?? false)}}"
        data-language="{{ str_replace('_', '-', app()->getLocale()) }}"
        data-field-placeholder="{{ $field['placeholder'] }}"
        data-field-allow-clear="{{ var_export($field['allows_null']) }}"
        @include('crud::fields.inc.attributes', ['default_class' =>  'form-control select2_field'])
        >

        @if ($field['allows_null'])
            <option value="">-</option>
        @endif

        @if (isset($field['model']))
            @php
                $obj = new $field['model'];
                $first_level_items = $obj->with('children')->where('depth', '1')->orWhere('depth', null)->orderBy('lft', 'ASC')->get();
            @endphp

            @foreach ($first_level_items as $connected_entity_entry)
                @php
                    echoSelect2NestedEntry($connected_entity_entry, $field, $current_value);
                    echoSelect2NestedChildren($connected_entity_entry, $field, $current_value);
                @endphp
            @endforeach
        @endif
    </select>

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
        @bassetBlock('backpack/pro/fields/select2-nested-field.js')
        <script>
            function bpFieldInitSelect2NestedElement(element) {
                if (!element.hasClass("select2-hidden-accessible"))
                {
                    let isFieldInline = element.data('field-is-inline');
                    let placeholder = element.data('field-placeholder');
                    let allowClear = element.data('field-allow-clear');

                    element.select2({
                        theme: "bootstrap",
                        placeholder: placeholder,
                        allowClear: allowClear,
                        dropdownParent: isFieldInline ? $('#inline-create-dialog .modal-content') : $(document.body)
                    });
                }
            }
        </script>
        @endBassetBlock
    @endpush

{{-- End of Extra CSS and JS --}}
{{-- ########################################## --}}
