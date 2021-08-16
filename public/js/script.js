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
        formGroup.find('input').each(function(){
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
                    if(data.res){console.log(formData);
                        let prependStr =
                            '<div data-id="'+data.id+'">' +
                            '<li class="list-group-item d-flex justify-content-between bg-secondary" >' +
                                '<div class="input-group align-content-center">' + formData["Title"] + '</div>' +
                                '<div class="input-group mr-5 showinfo"><button type="button" class="btn form-control btn-success">Дополнительно</button></div>' +
                                '<div class="input-group ml-5 mr-5 drop"><button type="button" class="btn form-control btn-danger">Удалить</button></div>' +
                            '</li>' +
                            '<div class="info" hidden>' +
                                '<li class="list-group-item"">' +
                                    '<div class="d-flex">' +
                                        '<div class="input-group">Год: ' + formData["Release Year"] + '</div>' +
                                        '<div class="input-group">Формат: ' + formData["Format"] + '</div>' +
                                    '</div>' +
                                '</li>' +
                                '<li class="list-group-item">Актеры: '+ formData["Stars"] + '</li>' +
                            '</div></div>';
                        $('#movies').prepend(prependStr);
                        $('#movies').prepend('<center><h3>Фильм успешно удалён</h3></center>')
                        setTimeout(function (){
                            $('#movies center').remove();
                        }, 5000);
                    }else{
                        alert('Заполните все поля');
                    }
                }
            })
        }
    });

    $('body').on('click', '.drop', function(){
        if(confirm('Вы действительно хотите удалить этот фильм?')){
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
                        movieBlock.html('<center><h3>Фильм успешно удалён</h3></center>')
                        setTimeout(function (){
                            movieBlock.remove();
                        }, 5000);
                    }else{
                        alert('Не удалось удалить фильм');
                    }
                }
            });
        }
    });
});