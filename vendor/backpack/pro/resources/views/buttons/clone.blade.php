@if ($crud->hasAccess('clone', $entry))
	<a href="javascript:void(0)" onclick="cloneEntry(this)" bp-button="clone" data-route="{{ url($crud->route.'/'.$entry->getKey().'/clone') }}" class="btn btn-sm btn-link" data-button-type="clone"><i class="la la-copy"></i> <span>{{ trans('backpack::crud.clone') }}</span></a>
@endif

{{-- Button Javascript --}}
{{-- - used right away in AJAX operations (ex: List) --}}
{{-- - pushed to the end of the page, after jQuery is loaded, for non-AJAX operations (ex: Show) --}}
@push('after_scripts') @if (request()->ajax()) @endpush @endif
<script>
	if (typeof cloneEntry != 'function') {
	  $("[data-button-type=clone]").unbind('click');

	  function cloneEntry(button) {
	      // ask for confirmation before deleting an item
	      // e.preventDefault();
	      var button = $(button);
	      var route = button.attr('data-route');

          $.ajax({
              url: route,
              type: 'POST',
              success: function(result) {
                  // Show an alert with the result
                  new Noty({
                    type: "success",
                    text: "{!! trans('backpack::crud.clone_success') !!}"
                  }).show();

                  // Hide the modal, if any
                  $('.modal').modal('hide');

                  // if result has a redirect, redirect to that location string
                  if (result.redirect) {
                      window.location = result.redirect;
                  }


                  if (typeof crud !== 'undefined') {
                    crud.table.draw(false);
                  }
              },
              error: function(result) {
                  // Show an alert with the result
                  new Noty({
                    type: "warning",
                    text: "{!! trans('backpack::crud.clone_failure') !!}"
                  }).show();
              }
          });
      }
	}

	// make it so that the function above is run after each DataTable draw event
	// crud.addFunctionToDataTablesDrawEventQueue('cloneEntry');
</script>
@if (!request()->ajax()) @endpush @endif