$(document).ready(function()
{
	//usuwanie klasy zeby odkryć loading, klasa jest po to żeby strona działała z JS i bez JS
	$('.hidden-load').removeClass('hidden-load');
	$('.loading').hide();

	function ladowanie(option)
	{
		switch(option)
		{
			case 'start':
				$('.loading').fadeIn(250);
                $('.loading').html('Loading...');
			break;

			case 'stop':
				$('.loading').fadeOut(250);
			break;

            case 'error':
                $('.loading').html('Try again...');
            break;
		}
	}

    var edycja_aktywna = false //flaga zeby sprawdzac czy jest juz aktywna edycja komentarza, aby nie dopuscic edycji 2
    $('.ajax').click(function(e)
    {
    	e.preventDefault();
    	var url = $(this).attr('href');
    	var target = '#' + $(this).data('target');

    	if($(this).hasClass('remove') )
    	{
	        $.ajax(url,{
	        beforeSend: function() { ladowanie('start'); },
            error: function() { ladowanie('error'); },
	        success: function()
	        {
	        	ladowanie('stop');
	        	$(target).slideToggle(200);
	        }
	    	});
    	}
    	else if($(this).hasClass('vote'))
    	{
	        $.ajax(url,{
	        beforeSend: function() { ladowanie('start'); },
            error: function() { ladowanie('error'); },
	        success: function(data)
	        {
	        	ladowanie('stop');
	        	$(target).find('.votes').html(data);
	        }
	    	});
    	}
    	else if($(this).hasClass('edit-post'))
    	{
    		$.ajax({
    			url: url,
    			beforeSend: function() { ladowanie('start'); },
                error: function() { ladowanie('error'); },
    			success: function(data)
    			{
    				ladowanie('stop');
    				$(target).find('.post-content').slideToggle(100).html(data).slideToggle(200);
    				$(target).find('.story-manage').slideToggle(200);
				    $('.ajax-form').submit(function(e)
				    {
				    	e.preventDefault();
				    	var action = $(this).attr('action');
				    	var form = $(this);
				    	if($(this).hasClass('post-form'))
				    	{
					    	$.ajax({
					    		url: action,
					    		type: 'POST',
					    		data: form.serialize(),
					    		beforeSend: function() { ladowanie('start'); },
                                error: function() { ladowanie('error'); },
					    		success: function(data)
					    		{
					    			ladowanie('stop');
					    			$(target).find('.post-content').slideToggle(100).html(data).slideToggle(200);
					    			$(target).find('.story-manage').slideToggle(200);
					    		}
					    	});
				    	}
				    });

    			}
    		})
    	}
    	else if($(this).hasClass('edit-comment'))
    	{
            if(!edycja_aktywna)
            {
                $.ajax({
                url: url,
                beforeSend: function()
                {
                    ladowanie('start');
                    edycja_aktywna = true;
                },
                error: function()
                {
                    ladowanie('error');
                    edycja_aktywna = false;
                },
                success: function(data)
                {
                    ladowanie('stop');
                    $(target).find('.comment-content').slideToggle(100).html(data).slideToggle(200);
                    $(target).find('.comment-manage').slideToggle(200);
                    $('.ajax-form').submit(function(e)
                    {
                        e.preventDefault();
                        var action = $(this).attr('action');
                        var form = $(this);
                        if($(this).hasClass('comment-form-edit'))
                        {
                            $.ajax({
                                url: action,
                                type: 'POST',
                                data: form.serialize(),
                                beforeSend: function() { ladowanie('start'); },
                                error: function() { ladowanie('error'); },
                                success: function(data)
                                {
                                    edycja_aktywna = false;
                                    ladowanie('stop');
                                    var content = $(data).find('.comment-content').html()
                                    $(target).find('.comment-content').slideToggle(100).html( content ).slideToggle(200);
                                    $(target).find('.comment-manage').slideToggle(200);
                                }
                            });
                        }
                    });

                }
            })
            }
            else
                alert("Only 1 comment can be edit at the same time.");
    	}

    });


    $('.ajax-form').submit(function(e)
    {
    	e.preventDefault();
    	var action = $(this).attr('action');
    	var target = '#' + $(this).data('target');
    	var form = $(this);

    	if($(this).hasClass('comment-form-add'))
    	{
    		$.ajax({
    			url: action,
    			type: 'POST',
    			data: form.serialize(),
    			beforeSend: function() { ladowanie('start'); },
                error: function() { ladowanie('error'); },
    			success: function(data)
    			{
    				ladowanie('stop');
    				$(target).find('.comments .add-comment').after(data).toggle().slideToggle(200);
    			}
    		});
    	}
    	else if($(this).hasClass('post-form'))
    	{
    		$.ajax({
    			url: action,
    			type: 'POST',
    			data: form.serialize(),
    			beforeSend: function() { ladowanie('start'); },
                error: function() { ladowanie('error'); },
    			success: function(data)
    			{
    				ladowanie('stop');
    				$(target).html(data);
    			}
    		});
    	}
    	else if($(this).hasClass('vote-form'))
    	{

    		$.ajax({
    			url: action,
    			type: 'POST',
    			data: form.serialize(),
    			beforeSend: function() { ladowanie('start'); },
                error: function() { ladowanie('error'); },
    			success: function(data)
    			{
    				ladowanie('stop');
    				$(target).find('.votes').html(data);
    			}
    		});
    	}
    });

});