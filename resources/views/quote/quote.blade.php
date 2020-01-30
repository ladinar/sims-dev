@extends('template.template_admin-lte')
@section('content')
  <style type="text/css">
    .DTFC_LeftBodyLiner {
      overflow: hidden;
  }
  </style>

  <section class="content-header">
    <h1>
      Daftar Buku Admin (Quote Number)
    </h1>
    <ol class="breadcrumb">
      <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
      <li class="active">Admin</li>
      <li class="active">Quote Number</li>
    </ol>
  </section>

  <section class="content">
    @if (session('update'))
      <div class="alert alert-warning" id="alert">
          {{ session('update') }}
      </div>
        @endif

        @if (session('success'))
          <div class="alert alert-success notification-bar"><span>Notice : </span> {{ session('success') }}<button type="button" class="dismisbar transparant pull-right"><i class="fa fa-times fa-lg"></i></button><br>Get your Quote Number :<h4> {{$pops->quote_number}}</h4></div>
        @endif

        @if (session('alert'))
      <div class="alert alert-success" id="alert">
          {{ session('alert') }}
      </div>
    @endif

    <div class="box">
      <div class="box-header with-border">
        
          <div class="pull-right">
            @if(Auth::User()->id_position == 'ADMIN' || Auth::User()->id_division == 'SALES' || Auth::User()->id_position == 'DIRECTOR' || Auth::User()->id_position == 'MANAGER' && Auth::User()->id_division == 'TECHNICAL'  || Auth::User()->id_position == 'STAFF GA')
            <button type="button" class="btn btn-success pull-right" style="width: 100px" data-target="#modalAdd" data-toggle="modal"><i class="fa fa-plus"> </i> &nbspAdd Quote</button>
            @endif
            @if(Auth::User()->id_position == 'ADMIN' || Auth::User()->id_division == 'SALES' || Auth::User()->id_position == 'DIRECTOR' || Auth::User()->id_position == 'MANAGER' && Auth::User()->id_division == 'TECHNICAL' || Auth::User()->id_position == 'STAFF GA')
            @if($counts)
            <button type="button" class="btn btn-success pull-right" id="" data-target="#letter_backdate" data-toggle="modal" style="margin-right: 10px;width: 100px"><i class="fa fa-plus"> </i>&nbsp Back Date</button>
            @else
            <button type="button" class="btn btn-success pull-right disabled" id="" data-target="#letter_backdate" data-toggle="modal" style="margin-right: 10px;width: 100px"><i class="fa fa-plus"> </i>&nbsp Back Date</button>
            @endif
            @endif
          </div>
      </div>
      <div class="box-body">
        <div class="nav-tabs-custom">
          <ul class="nav nav-tabs" id="myTab">
            <li class="active"><a href="#tab_1" data-toggle="tab">All</a></li>
            <li><a href="#tab_2" data-toggle="tab">Backdate</a></li>
          </ul>

          <div class="tab-content">

            <div class="tab-pane active" id="tab_1">
              <div class="table-responsive">
                <table class="table table-bordered nowrap table-striped dataTable data" id="data_all" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>Quote Number</th>
                      <th>Position</th>
                      <th>Type of Letter</th>
                      <th>Month</th>
                      <th>Date</th>
                      <th>To</th>
                      <th>Attention</th>
                      <th>Title</th>
                      <th>Project</th>
                      <th>Description</th>
                      <th>From</th>
                      <th>Division</th>
                      <th>Project ID</th>
                      <th>Project Type</th>
                      <th>Note</th>
                      @if(Auth::User()->id_position == 'ADMIN' || Auth::User()->id_division == 'SALES' || Auth::User()->id_position == 'DIRECTOR' || Auth::User()->id_position == 'MANAGER' && Auth::User()->id_division == 'TECHNICAL')
                        <th>Action</th>
                      @endif
                    </tr>
                  </thead>
                  <tbody id="products-list" name="products-list">
                    @foreach($datas as $data)
                      @if($data->status_backdate == '')
                      <tr>
                        <td>{{ $data->quote_number }}</td>
                        <td>{{ $data->position }}</td>
                        <td>{{ $data->type_of_letter }}</td>
                        <td>{{ $data->month }}</td>
                        <td>{{ $data->date }}</td>
                        <td>{{ $data->to }}</td>
                        <td>{{ $data->attention }}</td>
                        <td>{{ $data->title }}</td>
                        <td>{{ $data->project }}</td>
                        <td>{{ $data->description }}</td>
                        <td>{{ $data->name }}</td>
                        <td>{{ $data->division }}</td>
                        <td>{{ $data->project_id }}</td>
                        <td></td>
                        <td>{{ $data->note }}</td>
                        @if(Auth::User()->id_position == 'ADMIN' || Auth::User()->id_division == 'SALES' || Auth::User()->id_position == 'DIRECTOR' || Auth::User()->id_position == 'MANAGER' && Auth::User()->id_division == 'TECHNICAL')
                          <td>
                            <!-- <button class="btn btn-sm btn-primary fa fa-search fa-lg" data-target="#modalEdit" data-toggle="modal" style="width: 40px;height: 40px;text-align: center;" onclick="quote('{{$data->quote_number}}','{{$data->position}}','{{$data->to}}','{{$data->attention}}','{{$data->title}}','{{$data->project}}')">
                            </button> -->
                            @if(Auth::User()->nik == $data->nik)
                            <button class="btn btn-xs btn-primary" style="vertical-align: top; width: 60px" data-target="#modalEdit" data-toggle="modal" onclick="quote('{{$data->quote_number}}','{{$data->position}}','{{$data->to}}','{{$data->attention}}','{{$data->title}}','{{$data->project}}')">&nbsp Edit
                            </button>
                            @else
                            <button class="btn btn-xs btn-primary disabled" style="vertical-align: top; width: 60px">&nbsp Edit
                            </button>
                            @endif

                            <!--  <a href="{{ url('delete?id_quote='. $data->id_quote) }}"><button class="btn btn-sm btn-danger fa fa-trash fa-lg" style="width: 40px;height: 40px;text-align: center;" onclick="return confirm('Are you sure want to delete this data?')">
                            </button></a> -->
                          </td>
                        @endif
                      </tr>
                      @endif
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>

            <div class="tab-pane" id="tab_2">
              <div class="table-responsive">
                <table class="table table-bordered nowrap table-striped dataTable data" id="data_backdate" width="100%" cellspacing="0">
                  <thead>
                    <tr>
                      <th>Quote Number</th>
                      <th>Position</th>
                      <th>Type of Letter</th>
                      <th>Date</th>
                      <th>To</th>
                      <th>Attention</th>
                      <th>Title</th>
                      <th>Project</th>
                      <th>Description</th>
                      <th>From</th>
                      <th>Division</th>
                      <th>Project ID</th>
                      <th>Project Type</th>
                      <th>Note</th>
                      @if(Auth::User()->id_position == 'ADMIN' || Auth::User()->id_division == 'SALES' || Auth::User()->id_position == 'DIRECTOR' || Auth::User()->id_position == 'MANAGER' && Auth::User()->id_division == 'TECHNICAL')
                        <th>Action</th>
                      @endif
                    </tr>
                  </thead>
                  <tbody id="products-list" name="products-list">
                    @foreach($datas as $data)
                    @if($data->status_backdate == 'F')
                    <tr>
                      <td>{{ $data->quote_number }}</td>
                      <td>{{ $data->position }}</td>
                      <td>{{ $data->type_of_letter }}</td>
                      <td>{{ $data->date }}</td>
                      <td>{{ $data->to }}</td>
                      <td>{{ $data->attention }}</td>
                      <td>{{ $data->title }}</td>
                      <td>{{ $data->project }}</td>
                      <td>{{$data->description}}</td>
                      <td>{{$data->name}}</td>
                      <td>{{$data->division}}</td>
                      <td>{{$data->project_id}}</td>
                      <td>{{$data->note}}</td>
                      @if(Auth::User()->id_position == 'ADMIN' || Auth::User()->id_division == 'SALES' || Auth::User()->id_position == 'DIRECTOR' || Auth::User()->id_position == 'MANAGER' && Auth::User()->id_division == 'TECHNICAL')
                        <td>
                          <!-- <button class="btn btn-sm btn-primary fa fa-search fa-lg" data-target="#modalEdit" data-toggle="modal" style="width: 40px;height: 40px;text-align: center;" onclick="quote('{{$data->quote_number}}','{{$data->position}}','{{$data->to}}','{{$data->attention}}','{{$data->title}}','{{$data->project}}')">
                          </button> -->
                          @if(Auth::User()->nik == $data->nik)
                          <button class="btn btn-xs btn-primary" style="vertical-align: top;" data-target="#modalEdit" data-toggle="modal" onclick="quote('{{$data->quote_number}}','{{$data->position}}','{{$data->to}}','{{$data->attention}}','{{$data->title}}','{{$data->project}}')">&nbsp Edit
                          </button>
                          @else
                          <button class="btn btn-xs btn-primary disabled" style="vertical-align: top;">&nbsp Edit
                          </button>
                          @endif
                        </td>
                      @endif
                    </tr>
                    @endif
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>

       <!--MODAL ADD-->  
<div class="modal fade" id="modalAdd" role="dialog">
      <div class="modal-dialog modal-lg">
        <!-- Modal content-->
        <div class="modal-content modal-md">
          <div class="modal-header">
            <h4 class="modal-title">Add Quote</h4>
          </div>
          <div class="modal-body">
            <form method="POST" action="{{url('/quote/store')}}" id="modalAddQuote" name="modalAddQuote">
              @csrf
              
              <div class="form-group">
                  <label>Position</label>
                  <select class="form-control" id="position" name="position" required>
                      <option value="TAM">TAM</option>
                      <option value="DIR">DIR</option>
                      <option value="MSM">MSM</option>
                  </select>
              </div>
              <div class="form-group">
                  <label>Date</label>
                  <input type="date" class="form-control" id="date" name="date" required>
              </div>

              <div class="form-group">
                  <label>To</label>
                  <input class="form-control" placeholder="Enter To" id="to" name="to" required>
              </div>

              <div class="form-group">
                  <label>Attention</label>
                  <input class="form-control" placeholder="Enter Attention" id="attention" name="attention" >
              </div>

              <div class="form-group">
                  <label>Title</label>
                  <input class="form-control" placeholder="Enter Title" id="title" name="title" >
              </div>

              <div class="form-group">
                  <label>Project</label>
                  <input class="form-control" placeholder="Enter Project" id="project" name="project" >
              </div>         
              <div class="form-group">
                  <label for="">Description</label>
                  <textarea class="form-control" id="description" name="description" placeholder="Enter Description"></textarea>
              </div>
              <div class="form-group">
                <label for="">Division</label>
                <select type="text" class="form-control" placeholder="Select Division" name="division" id="division" required>
                    <option>PMO</option>
                    <option>MSM</option>
                    <option>Marketing</option>
                    <option>TEC</option>
                </select>
              </div>
              <div class="form-group">
                <label for="">Project ID</label>
                <input type="text" class="form-control" placeholder="Enter Project ID" name="project_id" id="project_id">
              </div>
              <div class="form-group">
                <label>Project Type</label>
                <select class="form-control" id="project_type" name="project_type" required style="width: 100%">
                  <option>--Choose Project Type--</option>
                  <option value="Supply Only">Supply Only</option>
                  <option value="Maintenance">Maintenance</option>
                  <option value="Implementation">Implementation</option>
                </select>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal"><i class=" fa fa-times"></i>&nbspClose</button>
                <button type="submit" class="btn btn-primary"><i class="fa fa-check"> </i>&nbspSubmit</button>
              </div>
          </form>
          </div>
        </div>
      </div>
</div>

<!-- BACKDATE -->
<div class="modal fade" id="letter_backdate" role="dialog">
    <div class="modal-dialog modal-lg">
      <div class="modal-content modal-md">
        <div class="modal-header">
          <h4 class="modal-title">Add Quote (Backdate)</h4>
        </div>
        <div class="modal-body">
          <form method="POST" action="{{url('/store_quotebackdate')}}" id="quote_backdate" name="quote_backdate">
            @csrf

          <div class="form-group">
            <label>Backdate Number</label>
            <select type="text" class="form-control" placeholder="Select Backdate Number" style="width: 100%" name="backdate_num" id="backdate_num" required>
              @foreach($backdate_num as $data)
              <option value="{{$data->quote_number}}">{{$data->quote_number}}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label for="">Position</label>
            <select type="text" class="form-control" placeholder="Select Position" name="position" id="position" required>
                <option value="TAM">TAM</option>
                <option value="DIR">DIR</option>
                <option value="MSM">MSM</option>
            </select>
          </div>
          <div class="form-group">
            <label for="">Date</label>
            <input type="date" class="form-control" name="date" id="date" required>
          </div>
          <div class="form-group">
            <label for="">To</label>
            <input type="text" class="form-control" placeholder="Enter To" name="to" id="to" required>
          </div> 
          <div class="form-group">
            <label for="">Attention</label>
            <input type="text" class="form-control" placeholder="Enter Attention" name="attention" id="attention">
          </div> 
          <div class="form-group">
            <label for="">Title</label>
            <input type="text" class="form-control" placeholder="Enter Title" name="title" id="title">
          </div>
          <div class="form-group">
            <label for="">Project</label>
            <input type="text" class="form-control" placeholder="Enter Project" name="project" id="project">
          </div>
          <div class="form-group">
            <label for="">Description</label>
            <textarea class="form-control" id="description" name="description" placeholder="Enter Description"></textarea>
          </div>
          <div class="form-group">
            <label for="">Division</label>
            <select type="text" class="form-control" placeholder="Select Division" name="division" id="division" required>
                <option>PMO</option>
                <option>MSM</option>
                <option>Marketing</option>
                <option>TEC</option>
            </select>
          </div>
          <div class="form-group">
            <label for="">Project ID</label>
            <input type="text" class="form-control" placeholder="Enter Project ID" name="project_id" id="project_id">
          </div>
          <div class="form-group">
            <label>Project Type</label>
            <select class="form-control" id="project_type" name="project_type" required style="width: 100%">
              <option>--Choose Project Type--</option>
              <option value="Supply Only">Supply Only</option>
              <option value="Maintenance">Maintenance</option>
              <option value="Implementation">Implementation</option>
            </select>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal"><i class=" fa fa-times"></i>&nbspClose</button>
            <button type="submit" class="btn btn-primary"><i class="fa fa-check"> </i>&nbspSubmit</button>
          </div>
        </form>
        </div>
      </div>
    </div>
</div>

<!--MODAL EDIT-->  
  <div class="modal fade" id="modalEdit" role="dialog">
    <div class="modal-dialog modal-lg">
      <!-- Modal content-->
      <div class="modal-content modal-md">
        <div class="modal-header">
          <h4 class="modal-title">Edit Quote</h4>
        </div>
        <div class="modal-body">
          <form method="POST" action="{{url('/quote/update')}}" id="modalEditQuote" name="modalQuote">
            @csrf
            <div class="form-group" hidden>
                <label>Quote Number</label>
                <input class="form-control" id="edit_quote_number" name="quote_number">
            </div>
            <div class="form-group">
                <label>To</label>
                <input class="form-control" id="edit_to" placeholder="Enter To" name="edit_to" >
            </div>

            <div class="form-group">
                <label>Attention</label>
                <input class="form-control" id="edit_attention" placeholder="Enter Attention" name="edit_attention" >
            </div>

            <div class="form-group">
                <label>Title</label>
                <input class="form-control" id="edit_title" placeholder="Enter Title" name="edit_title" >
            </div>

            <div class="form-group">
                <label>Project</label>
                <input class="form-control" id="edit_project" name="edit_project" placeholder="Enter Project">
            </div> 
            <div class="form-group">
                  <label for="">Description</label>
                  <textarea class="form-control" id="edit_description" name="edit_description" placeholder="Enter Description"></textarea>
            </div>        
            <div class="form-group">
                <label>Project ID</label>
                <input class="form-control" id="edit_project_id" name="edit_project_id" placeholder="Enter Project ID">
            </div> 
            <div class="form-group">
                <label>Note</label>
                <input class="form-control" id="edit_note" name="edit_note" placeholder="Enter Note">
            </div> 
            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal"><i class=" fa fa-times"></i>&nbspClose</button>
              <button type="submit" class="btn btn-success"><i class="fa fa-check"> </i>&nbspUpdate</button>
              <!-- <button type="submit" class="btn btn-primary"><i class="fa fa-check"> </i>&nbspUpdate</button> -->
            </div>
        </form>
        </div>
      </div>
    </div>
  </div>
    
  </section>

@endsection

@section('script')
  <script type="text/javascript" src="{{asset('js/jquery.mask.min.js')}}"></script>
  <script type="text/javascript" src="{{asset('js/jquery.mask.js')}}"></script>
  <script type="text/javascript" src="{{asset('js/select2.min.js')}}"></script>
  <script type="text/javascript" src="{{asset('js/dataTables.fixedColumns.min.js')}}"></script>
  <script type="text/javascript">
    function quote(quote_number,position,to,attention,title,project,description, project_id,note) {
      $('#edit_quote_number').val(quote_number);
      $('#edit_position').val(position);
      $('#edit_to').val(to);
      $('#edit_attention').val(attention);
      $('#edit_title').val(title);
      $('#edit_project').val(project);
      $('#edit_description').val(description);
      $('#edit_project_id').val(project_id);
      $('#edit_note').val(note);
    }

    $('#data_all').DataTable( {
      "order": [[ 0, "desc" ]],
      fixedColumns:   {
          leftColumns: 1
      },
      scrollX:true,
      pageLength: 20,
    });

    $('#data_backdate').DataTable({
      "order": [[ 0, "desc" ]],
      "scrollX":true,
      scrollCollapse: true,
      "pageLength": 20,
      fixedColumns:   {
       leftColumns: 1,
      }
    });

    $("#alert").fadeTo(2000, 500).slideUp(500, function(){
      $("#alert").slideUp(300);
    });

    // $(".dismisbar").click(function(){
    //   $(".notification-bar").slideUp(300);
    // }); 

    $('a[data-toggle="tab"]').on( 'shown.bs.tab', function (e) {
        // var target = $(e.target).attr("href"); // activated tab
        // alert (target);
        $($.fn.dataTable.tables( true ) ).css('width', '100%');
        $($.fn.dataTable.tables( true ) ).DataTable().columns.adjust().draw();
    }); 

    $(".dismisbar").click(function(){
      $(".notification-bar").slideUp(300);
    }); 

    $('#myTab a').click(function(e) {
      e.preventDefault();
      $(this).tab('show');
    });

    // store the currently selected tab in the hash value
    $("ul.nav-tabs > li > a").on("shown.bs.tab", function(e) {
      var id = $(e.target).attr("href").substr(1);
      window.location.hash = id;
    });

    $("#backdate_num").select2();

    // on load of the page: switch to the currently selected tab
    var hash = window.location.hash;
    $('#myTab a[href="' + hash + '"]').tab('show');

  </script>
@endsection