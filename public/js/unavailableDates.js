// ADD Dates FORM
$( "#btn-add-item" ).click(function() {
    document.querySelector(".create-form").classList.add("active");
    document.querySelector(".overlay").classList.add("active");
});

$(".overlay").click(function(){
document.querySelector(".create-form").classList.remove("active");
document.querySelector(".overlay").classList.remove("active");
});

$(".create-form .close-btn").click(function(){
document.querySelector(".create-form").classList.remove("active");
document.querySelector(".overlay").classList.remove("active");
});

var tempArray = [];

// EDIT HOURS FORM
$(".btn-edit-item" ).click(function() {
    document.querySelector(".edit-form").classList.add("active");
    document.querySelector(".overlay").classList.add("active");

    $tr = $(this).closest('.tr');
    
    var data = $tr.children('.td').map(function(){
        return $(this).text();
    }).get();

    console.log(data);

    $('#updateUnavailableDateId').val(data[0])
    $('#updateDate').val(data[1])
    $('#updateDescription').val(data[2])
});

$(".overlay").click(function(){
    document.querySelector(".edit-form").classList.remove("active");
    document.querySelector(".overlay").classList.remove("active");
});

$(".edit-form .close-btn").click(function(){
    document.querySelector(".edit-form").classList.remove("active");
    document.querySelector(".overlay").classList.remove("active");
});



$('.btn-delete').on('click', function(e){
    e.preventDefault();
    const href = $(this).attr('href')

    Swal.fire({
        title: 'Delete date?',
        text: 'Are you sure you want to delete this date?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Yes'
    }).then((result) =>{
        if(result.value){
            document.location.href = href;
        }
    })
})