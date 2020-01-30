@extends('template.template_admin-lte')
@section('content')

<section class="content-header">
  <h1>
    Inventory
  </h1>
  <ol class="breadcrumb">
    <li><a href="#"><i class="fa fa-dashboard"></i> Home</a></li>
    <li class="active">Inventory</li>
  </ol>
</section>

<section class="content">

  @if (session('update'))
    <div class="alert alert-warning" id="alert">
        {{ session('update') }}
    </div>
  @endif

  @if (session('success'))
    <div class="alert alert-primary" id="alert">
        {{ session('success') }}
    </div>
  @endif

  @if (session('alert'))
    <div class="alert alert-success" id="alert">
        {{ session('alert') }}
    </div>
  @endif

  <div class="box">
    <div class="box-header">
      <div class="btn-group float-right">
        <button type="button" class="btn btn-warning dropdown-toggle" data-toggle="dropdown">
          <b><i class="fa fa-download"></i> Export</b>
        </button>
        <ul class="dropdown-menu" role="menu">
          <li><a href="#">PDF</a></li>
          <li><a href="#">EXCEL</a></li>
        </ul>
      </div>
    </div>

    <div class="box-body">
      <div class="col-md-6">
        <h6><i class="fa fa-table"></i>&nbsp Table Barang Masuk</h6>
        <div class="table-responsive">
          <table class="table table-bordered table-striped" id="data_Table" width="100%" cellspacing="0">
            <thead>
              <tr>
                <th>Nama Barang</th>
                <th>Serial Number</th>
                <th>Tgl masuk</th>
                <th>Note</th>
              </tr>
            </thead>
            <tbody id="products-list" name="products-list">
              @foreach($datam as $data)
              <tr>
                <td>{{$data->nama}}</td>
                <td>{{$data->serial_number}}</td>
                <td>{{$data->tgl_masuk}}</td>
                <td>{{$data->note}}</td>
              </tr>
              @endforeach
            </tbody>
            <tfoot>
            </tfoot>
          </table>
        </div>
      </div>
      <div class="col-md-6">
        <h6><i class="fa fa-table"></i>&nbsp Table Barang Keluar</h6>
        <div class="table-responsive">
          <table class="table table-bordered table-striped" id="data_Tables" width="100%" cellspacing="0">
            <thead>
              <tr>
                <th>Nama Barang</th>
                <th>Serial Number</th>
                <th>Tgl Keluar</th>
                <th>Note</th>
              </tr>
            </thead>
            <tbody id="products-list" name="products-list">
              @foreach($datas as $data)
              <tr>
                <td>{{$data->nama}}</td>
                <td>{{$data->serial_number}}</td>
                <td>{{$data->created_at}}</td>
                <td>{{$data->note}}</td>
              </tr>
              @endforeach
            </tbody>
            <tfoot>
            </tfoot>
          </table>
        </div>
      </div>
      </div>
    </div>
  </div>
</section>

<style type="text/css">
    .transparant{
      background-color: Transparent;
      background-repeat:no-repeat;
      border: none;
      cursor:pointer;
      overflow: hidden;
      outline:none;
      width: 25px;
    }

    .btnPR{
      color: #fff;
      background-color: #007bff;
      border-color: #007bff;
      width: 170px;
      padding-top: 4px;
      padding-left: 10px;
    }
</style>

@endsection

@section('script')
  <script type="text/javascript" src="{{asset('js/jquery.mask.min.js')}}"></script>
  <script type="text/javascript" src="{{asset('js/jquery.mask.js')}}"></script>
  <script type="text/javascript" src="{{asset('js/select2.min.js')}}"></script>
  <script type="text/javascript">
    function edit_pr(no,to,attention,title,project,description,from,issuance,project_id) {
      $('#edit_no_pr').val(no);
      $('#edit_to').val(to);
      $('#edit_attention').val(attention);
      $('#edit_title').val(title);
      $('#edit_project').val(project);
      $('#edit_description').val(description);
      $('#edit_from').val(from);
      $('#edit_issuance').val(issuance);
      $('#edit_project_id').val(project_id);
    }

    $("#alert").fadeTo(2000, 500).slideUp(500, function(){
         $("#alert").slideUp(300);
    });

    $('#data_Table').DataTable( {

    });

    $('#data_Tables').DataTable( {
    });
  </script>
@endsection