$(document).ready(function(){
    $('body').on('click', '.showinfo' ,function(){
        let showMovieInfo = $(this).closest('div[data-id]').find(".info")
        if(showMovieInfo.prop("hidden")){
            showMovieInfo.prop("hidden",false);
        }else{
            showMovieInfo.prop("hidden",true);
        }
    });

    $('#searchMovieByName input[name="Title"]').keyup(function(){
        let input = $(this)
        $('.searchTitle').each(function(index){
            let search = $(this).text().toLowerCase().includes(input.val().toLowerCase());
            if(search)
                $(this).closest('div[data-id]').show();
            else
                $(this).closest('div[data-id]').hide();
        });
    });

    $('#searchMovieByName input[name="Stars"]').keyup(function(){
        let input = $(this)
        $('.searchStars').each(function(index){
            let search = $(this).text().toLowerCase().includes(input.val().toLowerCase());
            if(search)
                $(this).closest('div[data-id]').show();
            else
                $(this).closest('div[data-id]').hide();
        });
    });

    $('#addNewMovie').click(function (){
        let formGroup = $('#addBlock');
        let formData = {};
        let data = true;
        formGroup.find('.filter').each(function(){
            if($(this).val() != ""){
                formData[$(this).prop('name')] = $(this).val();
            }else{
                data = false
            }
        });
        if(data){
            $.ajax({
                type: 'post',
                url: '/addMovie',
                dataType: 'JSON',
                data: formData,
                success: function(data){
                    if(data.res){
                        let prependStr =
                            '<div data-id="'+data.id+'">' +
                            '<li class="list-group-item d-flex justify-content-between bg-secondary" >' +
                                '<div class="input-group align-content-center">' + data.data["Title"] + '</div>' +
                                '<div class="input-group mr-5 showinfo"><button type="button" class="btn form-control btn-success">??????????????????????????</button></div>' +
                                '<div class="input-group ml-5 mr-5 drop"><button type="button" class="btn form-control btn-danger">??????????????</button></div>' +
                            '</li>' +
                            '<div class="info" hidden>' +
                                '<li class="list-group-item"">' +
                                    '<div class="d-flex">' +
                                        '<div class="input-group">??????: ' + data.data["Release Year"] + '</div>' +
                                        '<div class="input-group">????????????: ' + data.data["Format"] + '</div>' +
                                    '</div>' +
                                '</li>' +
                                '<li class="list-group-item">????????????: '+ data.data["Stars"] + '</li>' +
                            '</div></div>';
                        $('#movies').prepend(prependStr);
                    }
                    alert(data.message);
                }
            })
        }
    });

    $('#addBlock input[name="Stars"]').on('input', function() {
        $(this).val($(this).val().replace(/[^??-????-??a-zA-Z,-]/g, ''))
    });

    $('body').on('click', '.drop', function(){
        if(confirm('???? ?????????????????????????? ???????????? ?????????????? ???????? ???????????')){
            let movieBlock = $(this).closest('div[data-id]');
            $.ajax({
                type: 'post',
                url: '/deleteMovie',
                dataType: 'JSON',
                data: {
                    id: movieBlock.data('id')
                },
                success: function(res){
                    if(res){
                        movieBlock.html('<center><h3>?????????? ?????????????? ????????????</h3></center>')
                        setTimeout(function (){
                            movieBlock.remove();
                        }, 5000);
                    }else{
                        alert('???? ?????????????? ?????????????? ??????????');
                    }
                }
            });
        }
    });
});