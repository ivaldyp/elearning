<li class="dropdown">
	<a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#"> 
	 <img src="{{ config('app.openfileimgdefault') }}32" width="36" class=" img-circle" alt="img">

	  <b class="hidden-xs pull-right">Welcome</b><span class="caret"></span> </a>
	<ul class="dropdown-menu dropdown-user animated flipInY">
		<li>
			<div class="dw-user-box">                
				<div class="u-text">
					<h4>
						@if(Auth::user()->usname_skpd)
						{{ $_SESSION['user_data']['deskripsi_user'] }}
						@elseif(Auth::user()->usname_admin)
						{{ $_SESSION['user_data']['usname'] }}
						@elseif(Auth::user()->id_emp)
						{{ $_SESSION['user_data']['nm_emp'] }}
						@endif
					</h4>
					<h4 class="text-muted">
						@if(Auth::user()->usname_skpd)
							@if($_SESSION['user_data']['TLEVEL'] == 2)
							P3B
							@elseif($_SESSION['user_data']['TLEVEL'] == 3)
							Pengurus Barang
							@endif
						@elseif(Auth::user()->usname_admin)
						{{ $_SESSION['user_data']['idgroup'] }}
						@elseif(Auth::user()->id_emp)
						{{ $_SESSION['user_data']['idgroup'] }}
						@endif
					</h4>
				</div>
			</div>
		</li>
		<li role="separator" class="divider"></li>
		<li><a href="#" data-toggle="modal" data-target="#modal-password"><i class="ti-key"></i> Ubah Password</a></li>

		<!-- <li><a href="#"><i class="ti-email"></i> Inbox</a></li> -->
		<li role="separator" class="divider"></li>
		<li class="user-footer">
		  <div class="pull-right p-r-30">
			<!-- <a href="{{ route('logout') }}" class="btn btn-danger btn-flat">Sign out</a> -->
			<!-- <a class="dropdown-item btn btn-danger" href="{{ route('logout') }}"
			  onclick="event.preventDefault();
			  document.getElementById('logout-form').submit();">
			  {{ __('Logout') }}
			</a> -->
			<a class="dropdown-item btn btn-danger" href="{{ url('logout') }}">
			  {{ __('Logout') }}
			</a>

			<form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
			  @csrf
			</form>
		  </div>
		</li>
	</ul>
	<!-- /.dropdown-user -->
</li>
<div id="modal-password" class="modal fade" role="dialog" data-backdrop="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<form method="POST" action="/laporanbmd/home/password" class="form-horizontal">
			@csrf
				<div class="modal-header">
					<h4 class="modal-title"><b>Ubah Password</b></h4>
				</div>
				<div class="modal-body">
					<h4>Masukkan password baru  </h4>

					<div class="form-group col-md-12">
						<label for="passmd5" class="col-md-2 control-label"> Password </label>
						<div class="col-md-8">
							<input autocomplete="off" type="text" name="passmd5" class="form-control" required>
						</div>
					</div>

					<div class="clearfix"></div>
				</div>
				<div class="modal-footer">
					<button type="submit" class="btn btn-danger pull-right">Simpan</button>
					<button type="button" class="btn btn-default pull-right" style="margin-right: 10px" data-dismiss="modal">Close</button>
				</div>
			</form>
		</div>
	</div>
</div>
<!-- /.dropdown -->