            <div class="panel-body">
              <div class="form-horizontal">
                <div class="row">
                  <div class="col-md-8">
                  	@if (count($errors))
                    <div class="alert alert-danger">
                     <ul>
                      @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                      @endforeach
                    </ul>
                  </div>
                  @endif	
                  <div class="form-group">
                    <label for="name" class="control-label col-md-3">Name</label>
                    <div class="col-md-8">
                     {!! Form::text('name',null, ['class' => 'form-control']) !!}
                   </div>
                 </div>

                 <div class="form-group">
                  <label for="company" class="control-label col-md-3">Company</label>
                  <div class="col-md-8">
                    {!! Form::text('company',null, ['class' => 'form-control']) !!}
                  </div>
                </div>

                <div class="form-group">
                  <label for="email" class="control-label col-md-3">Email</label>
                  <div class="col-md-8">
                    {!! Form::text('email',null, ['class' => 'form-control']) !!}
                  </div>
                </div>

                <div class="form-group">
                  <label for="phone" class="control-label col-md-3">Phone</label>
                  <div class="col-md-8">
                    {!! Form::text('phone',null, ['class' => 'form-control']) !!}
                  </div>
                </div>

                <div class="form-group">
                  <label for="name" class="control-label col-md-3">Address</label>
                  <div class="col-md-8">
                    {!! Form::text('address',null, ['class' => 'form-control', 'rows' => 2]) !!}
                  </div>
                </div>
                <div class="form-group">
                  <label for="group" class="control-label col-md-3">Group</label>
                  <div class="col-md-5">
                   {!! Form::select('group_id', App\Group::orderBy('name','asc')->pluck('name','id'), null, ['class' =>'form-control']) !!}
                 </div>
                 <div class="col-md-3">
                  <a href="#" id="add-group-btn" class="btn btn-default btn-block">Add Group</a>
                </div>
              </div>
              <div class="form-group" id="add-new-group">
                <div class="col-md-offset-3 col-md-8">
                  <div class="input-group">
                    <input type="text" name="new_group" id="new_group" class="form-control">
                    <span class="input-group-btn">
                      <a href="#" id="add-new-btn" class="btn btn-default">
                        <i class="glyphicon glyphicon-ok"></i>
                      </a>
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-4">
              <div class="fileinput fileinput-new" data-provides="fileinput">
                <div class="fileinput-new thumbnail" style="width: 150px; height: 150px;">
                  <?php $photo = ! empty($contact->photo) ? $contact->photo : 'default.png' ?>
                  {!! Html::image('uploads/' . $photo, "Choose photo", ['width'=>150, 'height'=>150]) !!}
                </div>
                <div class="fileinput-preview fileinput-exists thumbnail" style="max-width: 200px; max-height: 150px;"></div>
                <div class="text-center">
                  <span class="btn btn-default btn-file"><span class="fileinput-new">Choose Photo</span><span class="fileinput-exists">Change</span>{!! Form::file('photo') !!}</span>
                  <a href="#" class="btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="panel-footer">
        <div class="row">
          <div class="col-md-8">
            <div class="row">
              <div class="col-md-offset-3 col-md-6">
                <button type="submit" class="btn btn-primary">{{ ! empty($contact->id) ? "Update" : "Save" }}</button>
                <a href="#" class="btn btn-default">Cancel</a>
              </div>
            </div>
          </div>
        </div>
      </div>

      @section('form-script')

      <script>
      $("#add-new-group").hide();
      $('#add-group-btn').click(function () {      
        $("#add-new-group").slideToggle(function() {
          $('#new_group').focus();
        });
        return false;
      });

      $("#add-new-btn").click(function() {

        var newGroup = $("#new_group");
        var inputGroup = newGroup.closest('.input-group');

        $.ajax({
          url: "{{ route("groups.store") }} ",
          method: 'post',
          data: {
            name: $("#new_group").val(),
            _token: $("input[name=_token]").val()
          },
          success: function (group){
            if(group.id != null){
              inputGroup.removeClass('has-error');
              inputGroup.next('.text-danger').remove();

              var newOption = $('<option></option>')
              .attr('value', group.id)
              .attr('selected', true)
              .text(group.name);


              $("select[name=group_id]")
              .append( newOption );

              newGroup.val("");
            }
          },
          error: function (xhr){
            var errors = xhr.responseJSON;
            var error = errors.name[0];
            if(error){

              inputGroup.next('.text-danger').remove();

              inputGroup
              .addClass('has-error')
              .after('<p class="text-danger">' + error + '</p>');
            }
          }
        });
          });
            </script>

            @endsection