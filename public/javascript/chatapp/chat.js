$(function()
{
	var person_template_item, notification_template_item;
	initTemplates();
	initTabContent();
	loadFriendlist();

	$('#people_search_btn').click(function(e)
	{
		e.preventDefault();
		var form	=	$('#people_search_form');
		var url		=	form.attr('action');
		var data	=	form.serialize();

		$.ajax
		({
			url: url,
			method: 'POST',
			data: data,
			dataType: 'json',
			success: function(response)
			{
				console.log(response);
				hideResultContainers();

				if(response.length == 0)
					showNoResultsContainer();
				else
				{
					setTimeout(function()
					{
						$('#people_list_group').empty();
						$.each(response, function(key, val)
						{
							console.log('t');
							var item = person_template_item.clone();
							item.find('.person_image').attr('src', server_a + val.profile_image);
							item.find('.person_dn').text(val.name);
							item.find('.person_username').text(val.username);
							$('#people_list_group').append(item);
							$('#people_list').fadeIn('fast');
						});
					}, 500);
				}
			},

			error: function(xhr, response, error)
			{
				console.log(xhr.responseText);
			}
		});
	});

	$('.user_profile_image, #change_bg_label').hover(function()
	{
		$('.user_profile_image').addClass('profile_image_fade');
		$('#change_bg_label').removeClass('hide');
	}, function()
	{
		$('.user_profile_image').removeClass('profile_image_fade');
		$('#change_bg_label').addClass('hide');
	});

	$('.user_profile_image, #change_bg_label').click(function()
	{
		$('#dp_path_input').val('');
		$('#change_dp_alert').hide();
		$('#dp_change_modal').modal('show');
	});

	$('#load_new_dp_btn').click(function(e)
	{
		e.preventDefault();
		var imagePath	=	$('#dp_path_input').val();
		$('.new_dp_image').attr('src', imagePath);
		$('#dp_path_input').focus();
	});


	$('#save_dp_change').click(function()
	{
		var form	=	$('#save_dp_form');
		var url		=	form.attr('action');
		var data	=	form.serialize();
		
		$.ajax
		({
			url: url,
			data: data,
			method: 'POST',
			dataType: 'json',
			success: function(response)
			{
				$('#change_dp_alert').removeClass('alert-success');
				$('#change_dp_alert').removeClass('alert-danger');
				$('#change_dp_alert_message').text(response.message);

				if(response.status)
					$('#change_dp_alert').addClass('alert-success');
				else
					$('#change_dp_alert').addClass('alert-danger');

				$('#change_dp_alert').show();

				if(response.status)
				{
					var newSrc = $('#dp_path_input').val();
					setTimeout(function()
					{
						
						$('#dp_change_modal').modal('hide');
						$('.user_profile_image').attr('src', newSrc);
					}, 1000);
				}
			},

			error: function(xhr, response, error)
			{
				console.log(xhr.responseText);
			}
		});
	});

	$('#friends_tab_header').click(function()
	{
		loadFriendlist();
		showTab('#friends_tab');
	});

	$('#notifications_tab_header').click(function()
	{
		loadNotificationList();
		showTab('#notifications_tab');
	});

	$('#settings_tab_header').click(function()
	{
		var fetchSettingsURL	=	$(this).find('a').attr('href');

		$.getJSON(fetchSettingsURL, function(response)
		{
			var tabContent	=	$('#settings_tab');
			tabContent.find('input[name="user_dn"]').val(response.user_dn);
			tabContent.find('input[name="user_dp"]').val(response.user_dp);
			tabContent.find('input[name="user_email"]').val(response.user_email);

			showTab('#settings_tab');
		});
	});

	$('#settings_update_btn').click(function(e)
	{
		var ladda = Ladda.create(this);
		ladda.start();

		e.preventDefault();
		var form	=	$('#settings_update_form');
		var url		=	form.attr('action');
		var data	=	form.serialize();
		var dp		=	form.find('input[name="user_dp"]').val();
		var dn		=	form.find('input[name="user_dn"]').val();

		console.log(data);

		$.ajax
		({
			url: url,
			data: data,
			method: 'POST',
			dataType: 'json',
			success: function(response)
			{
				console.log(response);
				setTimeout(function()
				{
					ladda.stop();

					showReturnMessage($('#settings_change_alert'), response.status, 
					response.message, $('#settings_change_message'));

					if(response.status)
					{
						$('.user_profile_image').attr('src', dp);
						$('#user_name_label').text(dn);
					}
				}, 500);
			},
			
			error: function(xhr, response, error)
			{
				console.log(xhr.responseText);
			}
		});
	});

	$(document).on('click', '.add_person_btn', function(e)
	{
		e.preventDefault();
		var btn			=	$(this);
		var url			=	btn.attr('href');
		var container	=	btn.closest('.person_list_item');
		var user		=	container.find('.person_username').text();
		var data		=	"user_id=" + user;

		console.log(data);
		$.ajax
		({
			url: url,
			data: data,
			method: 'POST',
			dataType: 'json',
			success: function(response)
			{
				showReturnMessage('#person_status_alert', response.status,
					response.message, '#person_status_message');
			},

			error: function(xhr, response, error)
			{
				console.log(xhr.responseText);
			}
		});	
		
	});

	$(document).on('click', '.remove_person_btn', function(e)
	{
		e.preventDefault();
		var btn			=	$(this);
		var url			=	btn.attr('href');
		var container	=	btn.closest('.person_list_item');
		var friendid	=	container.attr('data-friendid');
		var data		=	'friendship_id=' + friendid;

		console.log(data);
		$.ajax
		({
			url: url,
			data: data,
			method: 'POST',
			dataType: 'json',
			success: function(response)
			{

				showReturnMessage('#person_status_alert', response.status,
					response.message, '#person_status_message');

				if(response.status)
					container.remove();

				if($('.person_list_item').length == 0)
					showNoFriendsContainer();

			},

			error: function(xhr, response, error)
			{
				console.log(xhr.responseText);
			}
		});
	});


	$(document).on('click', '.read_notification_btn', function(e)
	{
		e.preventDefault();
		var url			=	$(this).attr('href');
		var container	=	$(this).closest('.notification_list_item');
		var notifyID	=	container.attr('data-notificationid');
		var data		=	'notification_id=' + notifyID;

		$.ajax
		({
			url: url,
			data: data,
			method: 'POST',
			dataType: 'json',
			success: function(response)
			{
				console.log(response);

				var notification	=	response.notification;	
				var modal			=	$('#notification_read_modal');
				var controls		=	$('#notification_controls').clone();
				var extra_controls	=	$('#notification_modal_extra_controls');

				$('#notif_modal_title').text(notification.title);
				$('#notif_modal_content').text(notification.content);
				modal.find('.modal-content').attr('data-notificationid', notifyID);
				$('#notification_modal_content').attr('data-friendshipid', notification.friendship_id);
				extra_controls.empty();
				extra_controls.append(controls);
				controls.removeClass('hide');
				modal.modal('show');
			},

			error: function(xhr, response, error)
			{
				console.log(xhr.responseText);
			}
		});
	});


	$(document).on('click', '.remove_notification_btn', function(e)
	{
		e.preventDefault();
		console.log('test');
		var btn			=	$(this);
		var url			=	btn.attr('href');
		var item		=	btn.closest('.notification_list_item');
		var notID		=	item.attr('data-notificationid');
		var data		=	'notification_id=' + notID;
		$('#notification_list').remove(item);
		console.log(item);
		$.ajax
		({
			data: data,
			url: url,
			method: 'POST',
			dataType: 'json',
			success: function(response)
			{
				console.log(response);
				btn.tooltip('hide');
				showReturnMessage('#notification_status_alert', response.status,
					response.message, '#notification_status_message');

				if(response.status)
					item.remove();
			},

			error: function(xhr, response, error)
			{
				console.log(xhr.responseText);
			}
		}); 
	});


	$(document).on('click', '#accept_request_btn', function(e)
	{
		e.preventDefault();
		respondFriendRequest(true);
	});

	$(document).on('click', '#reject_request_btn', function(e)
	{
		e.preventDefault();
		respondFriendRequest(false);
	});

	function respondFriendRequest(accept)
	{
		var url			=	$('#notification_controls').attr('data-respond-url');
		var friendid	=	$('#notif_modal_meta').attr('data-friendid');
		var data		=	'friendship_id=' + friendid + ';accept=' + accept;

		$.ajax
		({		
			url: url,
			data: data,
			method: 'POST',
			dataType: 'json',
			success: function(response)
			{
				console.log(response);
			},

			error: function(xhr, response, error)
			{
				console.log(xhr.responseText);
			}
		});
	}

	function loadNotificationList()
	{
		var fetchNotificationsURL	=	$('#notifications_tab_header').find('a').attr('href');
		
		$.getJSON(fetchNotificationsURL, function(response)
		{
			console.log(response);

			if(response.length == 0)
			{
				$('#notification_list_container').hide();
				$('#no_notifications_content').show();
			}

			else
			{
				$('#no_notifications_content').hide();

				var container = $('#notification_list_container');
				container.empty();
				$.each(response, function(key, val)
				{
					var item	=	notification_template_item.clone();
					if(val.type	==	'1')
						item.find('.notification_icon').addClass('glyphicon glyphicon-user');

					item.find('.notification_title').text(val.title);
					item.find('.notification_content').text(val.content);
					item.attr('data-notificationid', val.id);
					container.append(item);
				});
				
				initTooltips();
			}
			
		})
		.fail(function(xhr, response, error)
		{
			console.log(xhr.responseText);
		});
	}
	
	function loadFriendlist()
	{
		var fetchFriendsURL	=	$('#friends_tab_header').find('a').attr('href');
		
		$.getJSON(fetchFriendsURL, function(response)
		{
			if(response.length == 0)
				showNoFriendsContainer();
			
			else
			{
				$('#no_friends_content').hide();

				$('#people_list_group').empty();
				$.each(response, function(key, val)
				{
					var item = person_template_item.clone();
					item.find('.person_image').attr('src', server_a + val.profile_image);
					item.find('.person_dn').text(val.name);
					item.find('.person_username').text(val.username);
					item.attr('data-friendid', val.id);
					$('#people_list_group').append(item);
				});

				initTooltips();
			}
		})
		.fail(function(xhr, response, error)
		{
			console.log(xhr.responseText);
		});
	}
		
	function showTab(tab)
	{	
		$('.tab_content').hide();
		$(tab).fadeIn('fast');
	}

	function showNoFriendsContainer()
	{
		$('#people_list').hide();
		$('#no_friends_content').show();
		$('#people_search').focus();
	}

	function hideResultContainers()
	{
		$('#no_friends_content').hide();
		$('#no_results_container').hide();
	}

	function showNoResultsContainer()
	{
		$('#people_list').hide();
		$('#no_results_container').show();
		$('#people_search').focus();
	}

	function initTabContent()
	{
		$('#settings_tab').hide();
		$('#settings_change_alert').hide();
		$('#no_notifications_content').hide();
		$('#no_friends_content').hide();
		$('#notification_status_alert').hide();
		$('#person_status_alert').hide();
		$('#no_results_container').hide();
		$('#people_search').val('');
	}

	function initTemplates()
	{
		person_template_item		=	$('#person_item_template').clone();
		notification_template_item	=	$('#notification_item_template').clone();	
		$('#person_item_template').hide();
		$('#notification_item_template').hide();
	}
});
