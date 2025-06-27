/* ------------------------------------------------------------------------------
 *
 *  # Custom JS code
 *
 *  Place here all your custom js. Make sure it's loaded after app.js
 *
 * ---------------------------------------------------------------------------- */
function statusUpdate(id,status,name,statusName)
{

        var url = window.location+'/status';
        swal({
                title: "Are you sure ?",
                text: "Do you want to "+statusName+" this "+name+"?",
                type: "warning",
                customClass: "swal2-show",
                showCancelButton: !0,
                confirmButtonText: "Yes",
                cancelButtonText: "No",
                cancelButtonColor: "#0569a4",
                confirmButtonColor: "#0569a4",
                reverseButtons: !0
            },function(){
            $.ajax({
                url: url,
                type: 'POST',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {id:id,status:status},
                success: function (data) {
                    if(data.status=='successRedirect'){

                        swal("",data.message, "success");
                        setTimeout(function() {
                            window.location.href = data.redirect;
                        }, 3000);

                    } 
                    else if(data.status=='fail')
                    {
                        toastr.remove();
                        toastr.error(data.message);
                    }
                    else {
                        alert('Whoops Something went wrong!!');
                    }

                }
            });
        })
}

function readStatusUpdate(id)
{
    var url = window.location+'/read_status';
    $.ajax({
            url: url,
            type: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {id:id},
            success: function (data) {
                if(data.status=='successRedirect')
                {

                    toastr.success(data.message);

                    setTimeout(function() {
                        window.location.href = data.redirect;
                    }, 3000);

                }
            }
    });
}

$(document).on('submit', '.myForm', function(e) {


    e.preventDefault();
    $('#submitForm').css('pointer-events','none');
    $('#submitForm').css('background-color','#ccc');
    $(".spinner-border").css("display", "block");
    $(".body-div").css("opacity","1");
    var formid  = $(this).closest("form").attr('id');

    $(':input[type="submit"]').prop('disabled', true);

    $.ajax({
        url : $(this).attr('action'),
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },

        data: new FormData($("#"+formid)[0]),
        method : "POST",
        processData : false,
        contentType : false,
        success : function(data)
        {
            $(".spinner-border").css("display", "none");
            $(".body-div").css("opacity","1");
            $(':input[type="submit"]').prop('disabled', false);
            $('#submitForm').css('pointer-events','visible');
            $('#submitForm').css('background-color','#0569a4');
            toastr.remove();

            if(data.status=='successRedirect')
            {

               toastr.success(data.message);

               setTimeout(function() {
                    window.location.href = data.redirect;
                }, 3000);

            }
            if(data.status=='success')
            {
               toastr.success(data.message);
            }
            if(data.status=='fail')
            {
              toastr.remove();
                toastr.error(data.message);
                if(data.redirect)
                {
                    setTimeout(function() {
                        window.location.href = data.redirect;
                    }, 3000);
                }
            }

        },
        error:function(error)
        {
            $(':input[type="submit"]').prop('disabled', false);
            toastr.remove();
            toastr.error(error.message);
         }

    });
});



$(document).on('click', '#master', function(){
    if($(this).is(':checked',true))
    {
        $(".sub_chk").prop('checked', true);
    } else {
        $(".sub_chk").prop('checked',false);
    }
});

function deleteModel(id,deleteType = null)
{

    if(deleteType != null)
    {
        var allVals = [];
        $(".sub_chk:checked").each(function() {
            allVals.push($(this).attr('data-id'));
        });
        if(allVals.length <=0)
        {
            toastr.error("Please select any row for delete.");

        }
        else
        {
            $('#deleteModel').show();
            $('#deleteType').val(deleteType);
            var join_selected_values = allVals.join(",");
            $('#deleteId').val(join_selected_values);
        }
    }
    else
    {
        $('#deleteModel').show();
        $('#deleteId').val(id);
    }
}


function deleteData(type)
{

    var url = $('#deleteUrl').val();
    var id = $('#deleteId').val();
    var deleteType = $('#deleteType').val();
    $.ajax({
        type: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        url : url,
        data: {type:type,id:id,deleteType:deleteType},
        success : function(data)
        {
           
            $('#deleteModel').hide();
            if(data.status == 'fail')
            {
                if(data.redirect == null){
                    toastr.error(data.message);
                }
                else{
                    toastr.remove();
                   toastr.error(data.message);
                    setTimeout(function() {
                        window.location.href = data.redirect;
                    }, 3000);
                   
                }
                
            }
            else
            {
                swal("",data.message, "success");
                setTimeout(function() {
                    window.location.href = data.redirect;
                }, 3000);
            }



        },
        error:function(error){
            toastr.error(error.message);
         }

    });

}


$(document).on('click', '.restore', function(e) {
    var id = $(this).data('id');
    var url = $(this).data('url');
    var type = $(this).data('type');
    if(type == 'selectedRestore')
    {
        var allVals = [];
        $(".sub_chk:checked").each(function() {
            allVals.push($(this).attr('data-id'));
        });
        if(allVals.length <=0)
        {
            toastr.error("Please select any row for restore.");
            return false
        }
        else
        {
            var join_selected_values = allVals.join(",");
        }

    }

    swal({
        title: "Are you sure?",
        text: "Do you want to restore this ?",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: '#0569a4',
        confirmButtonText: 'Yes, restore it!',
        closeOnConfirm: true,
        closeOnCancel: true
    },

      function(){

        $.ajax({
              method : "POST",
              url : url,
              headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
              data : {ids:join_selected_values,id:id},
              success : function(data)
              {
                
                 self.hideProgress = function() {
                    swal.close();
                  };
                  if(data.status=='successRedirect')
                  {
                     toastr.success(data.message);
                     setTimeout(function() {
                          window.location.href = data.redirect;
                      }, 3000);
                  }
                  if(data.status=='success')
                  {
                     toastr.success(data.message);
                  }
                  if(data.status=='fail')
                  {
                      toastr.error(data.message);
                  }
              },
              error:function(error){
                  toastr.error(error.message);
               }

        });


       }
    );
});








