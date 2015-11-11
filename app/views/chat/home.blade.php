@extends('layout.master')

@section('head')
	@parent
	<title>ByteChat - Home</title>
	{{ HTML::style('css/chatapp/chat.css'); }}
@stop

@section('js')
	@parent
	{{ HTML::script('javascript/chatapp/chat.js'); }}
@stop

@section('content')

<script>
	var server_a	=	'{{ url("/"); }}/';
</script>

<!-- USER LEFT SIDE BAR -->
<div id='user_content' class='col-md-4'>
	<div class='profile_info'>
		<div id='profile_image_container' class='col-md-5'>
			{{ HTML::image(Auth::user()->profile_image, $alt='Profile image', ['class' => 'user_profile_image']); }}
			<span id='change_bg_label' class='hide'>Change</span>
		</div>
	
		<div class='col-md-7 user_info'>
			<h4><span class='glyphicon glyphicon-user'></span>{{ Auth::user()->username; }}</h4>
			<h4><span class='glyphicon glyphicon-star'></span><span id='user_name_label'>{{ Auth::user()->name; }}</span></h4>
			<h4><span class='glyphicon glyphicon-heart'></span>0 friends online</h4>
		</div>
	</div>

	<!-- CHANGE DISPLAY IMAGE MODAL -->
	<div id='dp_change_modal' class='modal fade'>
		<div class='modal-dialog'>
			<div class='modal-content'>
				<div class='modal-header'>
					<button class='close' data-dismiss='modal'><span>&times;</span></button>
					<h4 class='modal-title'>Change display image</h4>
				</div>
				
				<div class='modal-body'>

					<div id='change_dp_alert' class='alert alert-danger alert-dismissable fade in'>
						<button class='close' data-dismiss='alert'>x</button>
						<strong>Change display image notice</strong>
						<br>
						<p id='change_dp_alert_message'>test</p>
					</div>

					<div class='row'>
					<!-- CURRENT DISPLAY IMAGE -->
					<div class='col-md-6'>
					<div class='thumbnail'>
						{{ HTML::image(Auth::user()->profile_image, $alt='Profile image', ['width' => 128, 'height' => 128]); }}
						<div class='caption'>
							<h3>Current image</h3>
						</div>
					</div>
					</div>

					<!-- NEW DISPLAY IMAGE -->
					<div class='col-md-6'>
					<div class='thumbnail'>
						{{ HTML::image(Auth::user()->profile_image, $alt='Profile image', ['class' => 'new_dp_image', 'width' => 128, 'height' => 128]); }}
						<div class='caption'>
							<h3>New image</h3>
						</div>
					</div>
					</div>
					</div>

					<form id='save_dp_form' method='post' action='{{ URL::route("postChangeDP"); }}'>
						<div class='input-group full_input_group full_right_input_group'>		
							<input id='dp_path_input' type='text' class='full_input full_input_width' placeholder='Enter URL of new display image' name='new_dp_path' />
							<span class='input-group-btn'>
								<button id='load_new_dp_btn' class='btn btn-default'>Load</button>
							</span>
						</div>
					</form>
					<br>
					<center>
						<button data-dismiss='modal' class='btn btn-default'>Cancel</button>
						<button id='save_dp_change' class='btn btn-default btn-primary'>Save</button>
					</center>
				</div>
			</div>
		</div>
	</div>

	<!-- READ NOTIFICATION MODAL -->
	<div id='notification_read_modal' class='modal fade'>
		<div class='modal-dialog'>
			<div class='modal-content' data-notificationid=''>
				<div class='modal-header'>
					<button class='close' data-dismiss='modal'><span>&times;</span></button>
					<h4 class='modal-title'><span class='glyphicon glyphicon-bullhorn'></span> Read notification</h4>	
				</div>

				<div class='modal-body'>
					<div id='notification_modal_content'>
						<strong><h4 id='notif_modal_title'></h4></strong>
						<h5 id='notif_modal_content'></h5>
						<div id='notif_modal_extra_content'></div>

						<div id='notification_modal_controls'>
							<button class='btn btn-default' data-dismiss='modal'>Close</button>
							<div id='notification_modal_extra_controls'>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id='notification_controls' class='hide' data-respond-url='{{ URL::route("postRespondRequestFriend"); }}'>
		<button id='reject_request_btn' class='btn btn-default btn-danger'>Reject</button>
		<button id='accept_request_btn' class='btn btn-default btn-success'>Accept</button>
	</div>



	<div id='user_nav_container'>
		<ul id='user_nav' class='nav nav-tabs'>
			<li id='friends_tab_header' class='active'><a class='chat_nav_a blue_tab' href='{{ URL::route("getFriends"); }}'><h4>Friends</h4></a></li>
			<li class=''><a class='chat_nav_a green_tab' href='#'><h4>Channels</h4></a></li>`
			<li id='notifications_tab_header' class=''><a class='chat_nav_a red_tab' href='{{ URL::route("getNotifications"); }}'><h4>Notifications</h4></a></li>
			<li id='settings_tab_header' class=''><a class='chat_nav_a yellow_tab' href='{{ URL::route("getUserSettings"); }}'><h4>Settings</h4></a></li>
		</ul>

		<!-- PERSON CONTENT -->
		<div id='friends_tab' class='tab_content'>
	
			<!-- PERSON STATUS ALERT -->
			<div id='person_status_alert' class='alert alert-dismissable fade in'>
				<strong>User notice</strong>
				<p id='person_status_message'></p>
			</div>

			<!-- PEOPLE SEARCH FORM -->
			<form id='people_search_form' method='post' action='{{ URL::route("postFindPeople"); }}'>
				<div class='input-group full_input_group full_right_input_group'>
					<input type='text' id='people_search' class='full_input tab_search_input' placeholder='Find someone' name='search_term' />
					<span class='input-group-btn'>
						<button id='people_search_btn' class='btn btn-default'><span class='glyphicon glyphicon-search'></span></button>
					</span>
				</div>
			</form>

			<!-- NO FRIENDS CONTAINER -->
			<div id='no_friends_content'>
				<h4><span class='glyphicon glyphicon-info-sign'></span> You have no friends, try searching</h4>
			</div>

			<!-- NO RESULTS CONTAINER -->
			<div id='no_results_container'>
				<h4><span class='glyphicon glyphicon-info-sign'></span> No results found</h4>
			</div>

			<!-- PERSON LIST CONTAINER -->
			<div id='people_list'>
				<!-- PERSON LIST -->
				<ul id='people_list_group' class='list-group'>
					<li id='person_item_template' class='person_list_item list-group-item clearfix' data-friendid=''>
						<div class='col-md-6'>
							<div class='col-md-3'>
							<span class='list_image'><img class='person_image' src='' /></span>
							</div>
							<div class='col-md-9 person_info'>
								<h6 class='person_dn'></h6>
								<h6 class='person_username'></h6>
							</div>
						</div>

						<div class='col-md-6'>
							<div class='person_controls'>
								<a class='add_person_btn' data-trigger='manual' data-placement='bottom' data-title='' href='{{ URL::route("postRequestFriend"); }}'><span class='glyphicon glyphicon-plus'></span></a>
								<a data-trigger='manual' data-placement='bottom' data-title='' class='remove_person_btn' href='{{ URL::route("postRemoveFriend") }}'><span class='glyphicon glyphicon-remove'></span></a>
								<a class='message_person_btn' href=''><span class='glyphicon glyphicon-comment'></span></a>
							</div>
						</div>
					</li>
				
				</ul>
			</div>
		</div>

		<!-- SETTINGS CONTENT -->
		<div id='settings_tab' class='tab_content'>
			<div id='settings_change_alert' class='alert alert-danger alert-dismissable fade in'>
				<button class='close' data-dismiss='alert'>x</button>
				<strong>Settings change notice</strong>
				<br>
				<p id='settings_change_message'></p>
			</div>
			<form id='settings_update_form' method='post' action='{{ URL::route("postUpdatePersonalSettings"); }}'>
				<!-- DISPLAY NAME -->
				<div class='form-group'>
					<div class='row'>
						<div class='col-lg-5'>
							<h5><span class='glyphicon glyphicon-star'></span> Display name</h5>
						</div>
						<div class='col-lg-7'>
							<input type='text' class='full_input full_input_width' name='user_dn' placeholder='Name'
							data-trigger='focus' data-toggle='tooltip' data-placement='right' 
							title='Unique username 6-18 alphanumeric characters'>	
						</div>
					</div>
				</div>	

				<!-- DISPLAY IMAGE FIELD -->
				<div class='form-group'>
					<div class='row'>
						<div class='col-lg-5'>
							<h5><span class='glyphicon glyphicon-camera'></span> Display image</h5>
						</div>
						<div class='col-lg-7'>
							<input type='text' class='full_input full_input_width' name='user_dp' placeholder='Image URL'
							data-trigger='focus' data-toggle='tooltip' data-placement='right' 
							title='Unique username 6-18 alphanumeric characters'>	
						</div>
					</div>
				</div>

				<!-- EMAIL FIELD -->
				<div class='form-group'>
					<div class='row'>
						<div class='col-lg-5'>
							<h5><span class='glyphicon glyphicon-envelope'></span> Email</h5>
						</div>
						<div class='col-lg-7'>
							<input type='text' class='full_input full_input_width' name='user_email' placeholder='Email address'
							data-trigger='focus' data-toggle='tooltip' data-placement='right' 
							title='Unique username 6-18 alphanumeric characters'>	
						</div>
					</div>
				</div>
			</form>

			<center>
				<button id='settings_update_btn' data-style='expand-left' class='btn btn-lg btn-default btn-primary ladda-button'><span class='ladda-label'>Save settings</span></button>
			</center>
			</div>

			<!-- NOTIFICATION CONTENT -->
			<div id='notifications_tab' class='tab_content'>
				<div id='no_notifications_content'>
					<h4><span class='glyphicon glyphicon-info-sign'></span> You have no notifications</h4>
				</div>
	
				<div id='notification_status_alert' class='alert alert-dismissable fade in'>
					<strong>Notification notice</strong>
					<p id='notification_status_message'></p>
				</div>

				<!-- NOTIFICATION LIST -->
				<div id='notification_list_container'>
					<ul id='notification_list' class='list-group'>

						<!-- NOTIFICATION ITEM TEMPLATE -->
						<li id='notification_item_template' class='list-group-item notification_list_item clearfix' data-notificationid=''>
							<div class='col-md-9'>
								<div class='col-md-2'>
									<span class='notification_icon'></span>
								</div>
		
								<div class='col-md-10 notification_content_container'>
									<p class='notification_title'></p>
									<p class='notification_content'></p>
								</div>
							</div>

							<div class='col-md-3'>
								<div class='notification_controls'>
									<!-- REMOVE NOTIFICATION BUTTON -->
									<a class='remove_notification_btn' href='{{ URL::route("postRemoveNotification"); }}' data-placement='bottom'
									 data-toggle='tooltip' data-title='Remove notification'><span class='glyphicon glyphicon-remove'></span></a>

									<!-- READ NOTIFICATION BUTTON -->
									<a class='read_notification_btn' data-placement='bottom' data-toggle='tooltip' data-title='Read notification' 
									href='{{ URL::route("postReadNotification"); }}'><span class='glyphicon glyphicon-circle-arrow-right'></span></a>
								</div>
							</div>
						</li>
					</ul>
				</div>
			</div>

		</div>
	
	</div>
</div>

<!-- CHAT CONTENT -->
<div id='chat_content' class='col-md-8'>

</div>

@stop
