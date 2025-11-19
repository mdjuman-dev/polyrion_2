@extends('backend.layouts.master')
@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <div class="container-full">
        <!-- Main content -->
        <section class="content">
            <div class="row">
                <div class="col-12 col-xl-12">
                    <div class="customhtab_1 box-header px-0 pt-0 border-bottom-0 ">
                        <ul class="nav nav-pills ">
                            <li class=" nav-item "> <a href="index.html#" class="nav-link fw-500" data-bs-toggle="tab" aria-expanded="false">Crypto </a> </li>
                            <li class="mt-5 text-fade"> <a>/</a> </li>
                            <li class="nav-item "> <a href="index.html#" class="nav-link fw-500" data-bs-toggle="tab" aria-expanded="false">Coins</a> </li>
                            <li class="mt-5 text-fade"> <a>/</a> </li>
                            <li class="nav-item "> <a href="index.html#" class="nav-link active fw-500" data-bs-toggle="tab" aria-expanded="true ">Etherium</a></li>
                        </ul>
                        <ul class="box-controls_2 box-tools pull-right">
                            <li>
                                <div class="dropdown">
                                    <button class="btn btn-rounded btn-outline btn-light dropdown-toggle fw-600 mb-5 fs-14 p-1 px-3 me-1 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">Earn</button>
                                    <div class="dropdown-menu" style="">
                                        <a class="dropdown-item" href="index.html#"><i class="fa fa-plus"></i> New</a>
                                        <a class="dropdown-item" href="index.html#"><i class="fa fa-pencil"></i> Edit</a>
                                        <a class="dropdown-item" href="index.html#"><i class="fa fa-trash-o"></i> Delete</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="index.html#"><i class="fa fa-share-alt"></i> Share</a>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="dropdown">
                                    <button class="btn btn-rounded btn-outline btn-light dropdown-toggle fw-600 mb-5 fs-14 p-1 px-3 me-1 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">Gaming</button>
                                    <div class="dropdown-menu" style="">
                                        <a class="dropdown-item" href="index.html#"><i class="fa fa-plus"></i> New</a>
                                        <a class="dropdown-item" href="index.html#"><i class="fa fa-pencil"></i> Edit</a>
                                        <a class="dropdown-item" href="index.html#"><i class="fa fa-trash-o"></i> Delete</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="index.html#"><i class="fa fa-share-alt"></i> Share</a>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="dropdown">
                                    <button class="btn btn-rounded btn-outline btn-light dropdown-toggle fw-600 mb-5 fs-14 p-1 px-3 me-1 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">Exchange</button>
                                    <div class="dropdown-menu" style="">
                                        <a class="dropdown-item" href="index.html#"><i class="fa fa-plus"></i> New</a>
                                        <a class="dropdown-item" href="index.html#"><i class="fa fa-pencil"></i> Edit</a>
                                        <a class="dropdown-item" href="index.html#"><i class="fa fa-trash-o"></i> Delete</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="index.html#"><i class="fa fa-share-alt"></i> Share</a>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <div class="dropdown">
                                    <button class="btn btn-rounded btn-outline btn-light dropdown-toggle fw-600 mb-5 fs-14 p-1 px-3 me-1 border-0" type="button" data-bs-toggle="dropdown" aria-expanded="false">Buy</button>
                                    <div class="dropdown-menu" style="">
                                        <a class="dropdown-item" href="index.html#"><i class="fa fa-plus"></i> New</a>
                                        <a class="dropdown-item" href="index.html#"><i class="fa fa-pencil"></i> Edit</a>
                                        <a class="dropdown-item" href="index.html#"><i class="fa fa-trash-o"></i> Delete</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="index.html#"><i class="fa fa-share-alt"></i> Share</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>

                <div class="col-12 col-xl-6">
                    <div class=" box-body">
                        <div class="media align-items-center p-0 Eth_box justify-content-between ">
                            <div class=" d-flex m-0">
                                <a href="index.html#"><i class="cc ETH-alt" title="XRP"></i></a>
                                <div>
                                    <h3 class="mt-15 fw-500">Ethereum<sup class="text-fade">#2</sup></h3>
                                    <p class="main_font_1 mb-0 fw-500">$3,400.80
                                        <sup><small class=" text-success"><i class="fa fa-caret-up text-success me-1"></i>0.50%</small></sup>
                                    </p>
                                </div>
                            </div>
                        </div>
                        <div class="flexbox align-items-center mt-10">
                            <div>
                                <p class="no-margin fw-600">Tags</p>
                                <div class="clearfix">
                                    <a type="button" href="tags.html" class="waves-effect waves-light fw-600 btn btn-rounded btn-light mb-5 fs-14 p-1 px-3 me-1 mt-3">Mineable</a>
                                    <a type="button" href="tags.html" class="waves-effect waves-light fw-600 btn btn-rounded btn-light mb-5 fs-14 p-1 px-3 me-1 mt-3">PoW</button>
                                        <a type="button" href="tags.html" class="waves-effect waves-light fw-600 btn btn-rounded btn-light mb-5 fs-14 p-1 px-3 me-1 mt-3">Smart Contracts</a>
                                        <a type="button" href="tags.html" class="waves-effect waves-light fw-600 btn btn-rounded btn-light mb-5 fs-14 p-1 px-3 me-1 mt-3">Etherium</a>
                                        <a type="button" href="tags.html" class="btn btn-rounded fw-600 text-primary mb-5 fs-14 p-1 px-3 me-1 mt-3">View all</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-6">
                    <div class="box box-body">
                        <div class="row g-0 py-2">
                            <div class="col-12 col-lg-4 col-md-4">
                                <div class="box-body be-1 border-light">
                                    <div class="media align-items-center p-0 justify-content-between">
                                        <div class="d-flex m-0">
                                            <a href="index.html#"><i class="cc MONA" title="XRP"></i></a>
                                            <p class="mt-20 text-fade">#4548</p>
                                        </div>
                                    </div>
                                    <div class=" mt-10">
                                        <p class="fw-700 mb-0 fs-16 mt-30">Sanshu Inu</p>
                                        <p class="fs-14 text-fade fw-500">Sanshu</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4 col-md-4 hidden-down">
                                <div class="box-body be-1 border-light">
                                    <div class="media align-items-center p-0 justify-content-between">
                                        <div class="d-flex m-0">
                                            <a href="index.html#"><i class="cc SCOT" title="XRP"></i></a>
                                            <p class="mt-20 text-fade">#250</p>
                                        </div>
                                    </div>
                                    <div class=" mt-10">
                                        <p class="fw-700 mb-0 fs-16 mt-30">SCOT</p>
                                        <p class="fs-14 text-fade fw-500">SCOT</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-4 col-md-4 d-lg-block">
                                <div class="box-body btop-1">
                                    <div class="media align-items-center p-0 justify-content-between">
                                        <div class="d-flex m-0">
                                            <a href="index.html#"><i class="cc QTUM" title="XRP"></i></a>
                                            <p class="mt-20 text-fade">#50</p>
                                        </div>
                                    </div>
                                    <div class=" mt-10">
                                        <p class="fw-700 mb-0 fs-16 mt-30">QTUM</p>
                                        <p class="fs-14 text-fade fw-500">QTUM</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-xl-12">
                    <div class="">
                        <div class="row g-0">
                            <div class="col-12 col-lg-3 col-md-6">
                                <div class="box pull-up">
                                    <div class="box-body">
                                        <div class="flexbox align-items-center">
                                            <div>
                                                <p class="no-margin fw-700 fs-16">Market Cap</p>
                                            </div>
                                            <div class="card-controls text-end">
                                                <li class="dropdown">
                                                    <a data-bs-toggle="dropdown" href="index.html#"><i class="fa fa-info-circle fs-20 text-fade"></i></a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <p class="p-10 mb-0">Crypto Information</p>
                                                    </div>
                                                </li>
                                            </div>
                                        </div>
                                        <div class=" mt-20">
                                            <h2 class="fw-500 mb-0"><span class="text-fade">$</span> 258,963</h2>
                                            <p class="mb-5"><small class="fs-14 text-success "><i class="fa fa-caret-up text-success me-1"></i>5.50%</small></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-3 col-md-6  hidden-down">
                                <div class="box pull-up">
                                    <div class="box-body">
                                        <div class="flexbox align-items-center">
                                            <div>
                                                <p class="no-margin fw-700 fs-16">Fully Diluted</p>
                                            </div>
                                            <div class="card-controls text-end">
                                                <li class="dropdown">
                                                    <a data-bs-toggle="dropdown" href="index.html#"><i class="fa fa-info-circle fs-20 text-fade"></i></a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <p class="p-10 mb-0">Crypto Information</p>
                                                    </div>
                                                </li>
                                            </div>
                                        </div>
                                        <div class=" mt-20">
                                            <h2 class="fw-500 mb-0"><span class="text-fade">$</span> 963,459</h2>
                                            <p class="mb-5"><small class="fs-14 text-danger "><i class="fa fa-caret-down text-danger me-1"></i>1.25%</small></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-3 col-md-6 d-lg-block">
                                <div class="box pull-up">
                                    <div class="box-body">
                                        <div class="flexbox align-items-center">
                                            <div>
                                                <p class="no-margin fw-700 fs-16">Volume</p>
                                            </div>
                                            <div class="card-controls text-end">
                                                <li class="dropdown">
                                                    <a data-bs-toggle="dropdown" href="index.html#"><i class="fa fa-info-circle fs-20 text-fade"></i></a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <p class="p-10 mb-0">Crypto Information</p>
                                                    </div>
                                                </li>
                                            </div>
                                        </div>
                                        <div class=" mt-20">
                                            <h2 class="fw-500 mb-0"><span class="text-fade">$</span> 15,569,745</h2>
                                            <p class="mb-5"><small class="fs-14 text-success "><i class="fa fa-caret-up text-success me-1"></i>0.50%</small></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12 col-lg-3 col-md-6 d-lg-block">
                                <div class="box pull-up">
                                    <div class="box-body">
                                        <div class="flexbox align-items-center ">
                                            <div>
                                                <p class="no-margin fw-700 fs-16">Circulating Supply</p>
                                            </div>
                                            <div class="card-controls text-end">
                                                <li class="dropdown">
                                                    <a data-bs-toggle="dropdown" href="index.html#"><i class="fa fa-info-circle fs-20 text-fade"></i></a>
                                                    <div class="dropdown-menu dropdown-menu-end">
                                                        <p class="p-10 mb-0">Crypto Information</p>
                                                    </div>
                                                </li>
                                            </div>
                                        </div>
                                        <div class=" mt-20">
                                            <h2 class="fw-500 mb-0">852,147<span class="fs-30 fw-300 text-fade"> ETH</span></h2>
                                            <p class="mb-5"><small class="fs-14 text-fade">111,145,875</small></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-xl-9">

                    <div class="box box-body p-20">
                        <!-- Nav tabs -->
                        <div class="customhtab box-header px-0 border-bottom-0">
                            <ul class="nav nav-pills">
                                <li class=" nav-item"> <a href="index.html#navpills-1" class="nav-link active" data-bs-toggle="tab" aria-expanded="false">Overview</a> </li>
                                <li class="nav-item"> <a href="index.html#navpills-3" class="nav-link " data-bs-toggle="tab" aria-expanded="false">Market</a> </li>
                                <li class="nav-item"> <a href="index.html#navpills-2" class="nav-link" data-bs-toggle="tab" aria-expanded="true">Wallets</a></li>
                                <li class="nav-item"> <a href="index.html#navpills-4" class="nav-link" data-bs-toggle="tab" aria-expanded="true">Ratings</a> </li>
                                <li class="nav-item"> <a href="index.html#navpills-5" class="nav-link" data-bs-toggle="tab" aria-expanded="true">Analysis</a> </li>
                            </ul>
                            <ul class="box-controls_1 box-tools pull-right">
                                <li><a class="box-btn-close" href="index.html#"></a></li>
                                <li><a class="box-btn-slide" href="index.html#"></a></li>
                                <li><a class="box-btn-fullscreen" href="index.html#"></a></li>
                            </ul>
                        </div>
                        <hr class="mt-0 mb-20">
                        <!-- Tab panes -->
                        <div class="tab-content">
                            <div id="navpills-1" class="tab-pane active">
                                <div class="box">
                                    <div class="box-header with-border p-0">
                                        <ul class="nav nav-pills mb-20">
                                            <li class=" nav-item"> <a href="index.html#Overview-1" class="nav-link active" data-bs-toggle="tab" aria-expanded="false">Price</a> </li>
                                            <li class="nav-item"> <a href="index.html#Overview-2" class="nav-link" data-bs-toggle="tab" aria-expanded="false">Market cap</a> </li>
                                            <li class="nav-item"> <a href="index.html#Overview-3" class="nav-link" data-bs-toggle="tab" aria-expanded="true">Trading</a></li>
                                            <li class="nav-item"> <a href="index.html#Overview-4" class="nav-link" data-bs-toggle="tab" aria-expanded="true">USD</a> </li>
                                            <li class="nav-item"> <a href="index.html#Overview-5" class="nav-link" data-bs-toggle="tab" aria-expanded="true">BTC</a> </li>
                                        </ul>
                                        <div class="box-tools_1 pull-right">
                                            <div class="btn-group" id="realtime" data-toggle="btn-toggle">
                                                <button type="button" class="btn btn-default bg-light btn-sm" data-toggle="off">Off</button>
                                                <button type="button" class="btn btn-default bg-light btn-sm" data-toggle="on">On</button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="tab-content">
                                        <div id="Overview-1" class="tab-pane active">
                                            <div class="box-body pb-0">
                                                <div id="interactive" style="height: 300px;"></div>
                                            </div>
                                        </div>
                                        <div id="Overview-2" class="tab-pane">
                                            <div>
                                                <div class="col-12">
                                                    <div class="box">
                                                        <div class="box-body">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered no-margin">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Id</th>
                                                                            <th>Time</th>
                                                                            <th>Transactions</th>
                                                                            <th>Sent</th>
                                                                            <th>Fees</th>
                                                                            <th>Block Size</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>
                                                                                <a href="index.html#" class="hover-primary">550171</a>
                                                                            </td>
                                                                            <td>59&nbsp;seconds ago</td>
                                                                            <td>2,122</td>
                                                                            <td>50,456.89 BTC</td>
                                                                            <td>2.496 BTC</td>
                                                                            <td>456,123</td>
                                                                        </tr>

                                                                        <tr>
                                                                            <td>
                                                                                <a href="index.html#" class="hover-primary">550172</a>
                                                                            </td>
                                                                            <td>10&nbsp;minutes ago</td>
                                                                            <td>1,837</td>
                                                                            <td>5,145.51 BTC</td>
                                                                            <td>0.741 BTC</td>
                                                                            <td>963,987</td>
                                                                        </tr>

                                                                        <tr>
                                                                            <td>
                                                                                <a href="index.html#" class="hover-primary">550173</a>
                                                                            </td>
                                                                            <td>13&nbsp;minutes ago</td>
                                                                            <td>1,280</td>
                                                                            <td>20,789.50 BTC</td>
                                                                            <td>2.741 BTC</td>
                                                                            <td>456,147</td>
                                                                        </tr>

                                                                        <tr>
                                                                            <td>
                                                                                <a href="index.html#" class="hover-primary">550174</a>
                                                                            </td>
                                                                            <td>21&nbsp;minutes ago</td>
                                                                            <td>1,731</td>
                                                                            <td>10,009 BTC</td>
                                                                            <td>4.852 BTC</td>
                                                                            <td>852,484</td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <!-- /.box-body -->
                                                    </div>
                                                    <!-- /.box -->
                                                </div>
                                            </div>
                                        </div>
                                        <div id="Overview-3" class="tab-pane">
                                            <div>
                                                <div class="col-12">
                                                    <div class="box">
                                                        <div class="box-body">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered no-margin">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Id</th>
                                                                            <th>Time</th>
                                                                            <th>Transactions</th>
                                                                            <th>Sent</th>
                                                                            <th>Fees</th>
                                                                            <th>Block Size</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>
                                                                                <a href="index.html#" class="hover-primary">8526369</a>
                                                                            </td>
                                                                            <td>60&nbsp;seconds ago</td>
                                                                            <td>2,122</td>
                                                                            <td>22,819.638 BTC</td>
                                                                            <td>1.496 BTC</td>
                                                                            <td>975,573</td>
                                                                        </tr>

                                                                        <tr>
                                                                            <td>
                                                                                <a href="index.html#" class="hover-primary">45687</a>
                                                                            </td>
                                                                            <td>45&nbsp;minutes ago</td>
                                                                            <td>1,837</td>
                                                                            <td>8,410.154 BTC</td>
                                                                            <td>0.982 BTC</td>
                                                                            <td>950,027</td>
                                                                        </tr>

                                                                        <tr>
                                                                            <td>
                                                                                <a href="index.html#" class="hover-primary">789654</a>
                                                                            </td>
                                                                            <td>80&nbsp;minutes ago</td>
                                                                            <td>1,280</td>
                                                                            <td>15,541.708 BTC</td>
                                                                            <td>1.855 BTC</td>
                                                                            <td>974,440</td>
                                                                        </tr>

                                                                        <tr>
                                                                            <td>
                                                                                <a href="index.html#" class="hover-primary">1236547</a>
                                                                            </td>
                                                                            <td>45&nbsp;minutes ago</td>
                                                                            <td>1,731</td>
                                                                            <td>18,009.855 BTC</td>
                                                                            <td>1.963 BTC</td>
                                                                            <td>975,484</td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <!-- /.box-body -->
                                                    </div>
                                                    <!-- /.box -->
                                                </div>
                                            </div>
                                        </div>
                                        <div id="Overview-4" class="tab-pane">
                                            <div>
                                                <div class="col-12">
                                                    <div class="box">
                                                        <div class="box-body">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered no-margin">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Id</th>
                                                                            <th>Time</th>
                                                                            <th>Sent</th>
                                                                            <th>Fees</th>
                                                                            <th>Block Size</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>
                                                                                <a href="index.html#" class="hover-primary">550171</a>
                                                                            </td>
                                                                            <td>59&nbsp;seconds ago</td>
                                                                            <td>2,122</td>
                                                                            <td>2.496 USD</td>
                                                                            <td>456,123</td>
                                                                        </tr>

                                                                        <tr>
                                                                            <td>
                                                                                <a href="index.html#" class="hover-primary">550172</a>
                                                                            </td>
                                                                            <td>10&nbsp;minutes ago</td>
                                                                            <td>1,837</td>
                                                                            <td>0.741 USD</td>
                                                                            <td>963,987</td>
                                                                        </tr>

                                                                        <tr>
                                                                            <td>
                                                                                <a href="index.html#" class="hover-primary">550173</a>
                                                                            </td>
                                                                            <td>13&nbsp;minutes ago</td>
                                                                            <td>1,280</td>
                                                                            <td>2.741 USD</td>
                                                                            <td>456,147</td>
                                                                        </tr>

                                                                        <tr>
                                                                            <td>
                                                                                <a href="index.html#" class="hover-primary">550174</a>
                                                                            </td>
                                                                            <td>21&nbsp;minutes ago</td>
                                                                            <td>1,731</td>
                                                                            <td>4.852 USD</td>
                                                                            <td>852,484</td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <!-- /.box-body -->
                                                    </div>
                                                    <!-- /.box -->
                                                </div>
                                            </div>
                                        </div>
                                        <div id="Overview-5" class="tab-pane">
                                            <div>
                                                <div class="col-12">
                                                    <div class="box">
                                                        <div class="box-body">
                                                            <div class="table-responsive">
                                                                <table class="table table-bordered no-margin">
                                                                    <thead>
                                                                        <tr>
                                                                            <th>Id</th>
                                                                            <th>Time</th>
                                                                            <th>Sent</th>
                                                                            <th>Fees</th>
                                                                            <th>Block Size</th>
                                                                        </tr>
                                                                    </thead>
                                                                    <tbody>
                                                                        <tr>
                                                                            <td>
                                                                                <a href="index.html#" class="hover-primary">550171</a>
                                                                            </td>
                                                                            <td>59&nbsp;seconds ago</td>
                                                                            <td>2,122</td>
                                                                            <td>0.852 BTC</td>
                                                                            <td>745,123</td>
                                                                        </tr>

                                                                        <tr>
                                                                            <td>
                                                                                <a href="index.html#" class="hover-primary">550172</a>
                                                                            </td>
                                                                            <td>10&nbsp;minutes ago</td>
                                                                            <td>1,837</td>
                                                                            <td>1.745 BTC</td>
                                                                            <td>159,789</td>
                                                                        </tr>

                                                                        <tr>
                                                                            <td>
                                                                                <a href="index.html#" class="hover-primary">550173</a>
                                                                            </td>
                                                                            <td>13&nbsp;minutes ago</td>
                                                                            <td>8,741</td>
                                                                            <td>2.741 BTC</td>
                                                                            <td>852,456</td>
                                                                        </tr>

                                                                        <tr>
                                                                            <td>
                                                                                <a href="index.html#" class="hover-primary">550174</a>
                                                                            </td>
                                                                            <td>21&nbsp;minutes ago</td>
                                                                            <td>1,731</td>
                                                                            <td>45,009 BTC</td>
                                                                            <td>4.852 </td>
                                                                        </tr>
                                                                    </tbody>
                                                                </table>
                                                            </div>
                                                        </div>
                                                        <!-- /.box-body -->
                                                    </div>
                                                    <!-- /.box -->
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- /.box-body-->
                                </div>
                            </div>
                            <div id="navpills-2" class="tab-pane">
                                <div>

                                    <div class="box-body">
                                        <div class="table-responsive">
                                            <table class="table table-bordered no-margin">
                                                <thead>
                                                    <tr>
                                                        <th>Transaction Hash</th>
                                                        <th>BTC</th>
                                                        <th>Time</th>
                                                        <th>Miner Preference</th>
                                                        <th>Status</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            <a href="index.html#" class="hover-primary">
                                                                9d2c7b06bfa0
                                                            </a>
                                                            ...
                                                        </td>
                                                        <td>1.2126281 BTC</td>
                                                        <td>
                                                            <time class="timeago" datetime="2018-02-01T13:38:01Z" title="2018-02-01 13:38 GMT">2 minutes ago</time>
                                                        </td>
                                                        <td>medium</td>
                                                        <td><span class="label bg-success">Confirmed</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <a href="index.html#" class="hover-primary">
                                                                5de67415bfc6
                                                            </a>
                                                            ...
                                                        </td>
                                                        <td>0.20522881 BTC</td>
                                                        <td>
                                                            <time class="timeago" datetime="2018-02-01T13:38:01Z" title="2018-02-01 13:38 GMT">2 minutes ago</time>
                                                        </td>
                                                        <td>high</td>
                                                        <td><span class="label bg-warning">Unconfirmed</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <a href="index.html#" class="hover-primary">
                                                                733de15b3cec
                                                            </a>
                                                            ...
                                                        </td>
                                                        <td>2.02622033 BTC</td>
                                                        <td>
                                                            <time class="timeago" datetime="2018-02-01T13:38:01Z" title="2018-02-01 13:38 GMT">2 minutes ago</time>
                                                        </td>
                                                        <td>high</td>
                                                        <td><span class="label bg-success">Confirmed</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <a href="index.html#" class="hover-primary">
                                                                6793bcfa4f7f
                                                            </a>
                                                            ...
                                                        </td>
                                                        <td>2.43220578 BTC</td>
                                                        <td>
                                                            <time class="timeago" datetime="2018-02-01T13:38:00Z" title="2018-02-01 13:38 GMT">2 minutes ago</time>
                                                        </td>
                                                        <td>high</td>
                                                        <td><span class="label bg-danger">Canceled</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <a href="index.html#" class="hover-primary">
                                                                2c66087936b5
                                                            </a>
                                                            ...
                                                        </td>
                                                        <td>14.01099978 BTC</td>
                                                        <td>
                                                            <time class="timeago" datetime="2018-02-01T13:38:00Z" title="2018-02-01 13:38 GMT">2 minutes ago</time>
                                                        </td>
                                                        <td>high</td>
                                                        <td><span class="label bg-danger">Canceled</span></td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <a href="index.html#" class="hover-primary">
                                                                51935e53c294
                                                            </a>
                                                            ...
                                                        </td>
                                                        <td>0.3024534 BTC</td>
                                                        <td>
                                                            <time class="timeago" datetime="2018-02-01T13:38:00Z" title="2018-02-01 13:38 GMT">2 minutes ago</time>
                                                        </td>
                                                        <td>high</td>
                                                        <td><span class="label bg-warning">Unconfirmed</span></td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <!-- /.box-body -->
                                </div>
                            </div>
                            <div id="navpills-3" class="tab-pane">
                                <div>
                                    <div class="box-body no-padding">
                                        <!-- Tab panes -->
                                        <div class="tab-content">
                                            <div id="navpills-7" class="tab-pane active">
                                                <div class="table-responsive">
                                                    <table class="table table-hover no-margin text-center">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">Market</th>
                                                                <th scope="col">Price</th>
                                                                <th scope="col">Change</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>BTC - 12458</td>
                                                                <td>0.002548548</td>
                                                                <td>+12.85% <i class="fa fa-arrow-up text-success"></i></td>
                                                            </tr>
                                                            <tr>
                                                                <td>BTC - 02157</td>
                                                                <td>0.025486854</td>
                                                                <td>+05.15% <i class="fa fa-arrow-up text-success"></i></td>
                                                            </tr>
                                                            <tr>
                                                                <td>BTC - 12458</td>
                                                                <td>0.002548548</td>
                                                                <td>+12.85% <i class="fa fa-arrow-up text-success"></i></td>
                                                            </tr>
                                                            <tr>
                                                                <td>BTC - 12457</td>
                                                                <td>0.025845218</td>
                                                                <td>-02.01% <i class="fa fa-arrow-down text-danger"></i></td>
                                                            </tr>
                                                            <tr>
                                                                <td>BTC - 35487</td>
                                                                <td>0.021548754</td>
                                                                <td>+06.15% <i class="fa fa-arrow-up text-success"></i></td>
                                                            </tr>
                                                            <tr>
                                                                <td>BTC - 03254</td>
                                                                <td>0.025845845</td>
                                                                <td>-07.09% <i class="fa fa-arrow-down text-danger"></i></td>
                                                            </tr>
                                                            <tr>
                                                                <td>BTC - 12458</td>
                                                                <td>0.002548548</td>
                                                                <td>+12.85% <i class="fa fa-arrow-up text-success"></i></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div id="navpills-8" class="tab-pane">
                                                <div class="table-responsive">
                                                    <table class="table table-hover text-center">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">Market</th>
                                                                <th scope="col">Price</th>
                                                                <th scope="col">Change</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>ETH - 12458</td>
                                                                <td>0.002548548</td>
                                                                <td>+12.85% <i class="fa fa-arrow-up text-success"></i></td>
                                                            </tr>
                                                            <tr>
                                                                <td>ETH - 02157</td>
                                                                <td>0.025486854</td>
                                                                <td>+05.15% <i class="fa fa-arrow-up text-success"></i></td>
                                                            </tr>
                                                            <tr>
                                                                <td>ETH - 12457</td>
                                                                <td>0.025845218</td>
                                                                <td>-02.01% <i class="fa fa-arrow-down text-danger"></i></td>
                                                            </tr>
                                                            <tr>
                                                                <td>ETH - 35487</td>
                                                                <td>0.021548754</td>
                                                                <td>+06.15% <i class="fa fa-arrow-up text-success"></i></td>
                                                            </tr>
                                                            <tr>
                                                                <td>ETH - 03254</td>
                                                                <td>0.025845845</td>
                                                                <td>-07.09% <i class="fa fa-arrow-down text-danger"></i></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div id="navpills-9" class="tab-pane">
                                                <div class="table-responsive">
                                                    <table class="table table-hover text-center">
                                                        <thead>
                                                            <tr>

                                                                <th scope="col">Market</th>
                                                                <th scope="col">Price</th>
                                                                <th scope="col">Change</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>DASH - 12458</td>
                                                                <td>0.002548548</td>
                                                                <td>+12.85% <i class="fa fa-arrow-up text-success"></i></td>
                                                            </tr>
                                                            <tr>
                                                                <td>DASH - 02157</td>
                                                                <td>0.025486854</td>
                                                                <td>+05.15% <i class="fa fa-arrow-up text-success"></i></td>
                                                            </tr>
                                                            <tr>
                                                                <td>DASH - 12457</td>
                                                                <td>0.025845218</td>
                                                                <td>-02.01% <i class="fa fa-arrow-down text-danger"></i></td>
                                                            </tr>
                                                            <tr>
                                                                <td>DASH - 35487</td>
                                                                <td>0.021548754</td>
                                                                <td>+06.15% <i class="fa fa-arrow-up text-success"></i></td>
                                                            </tr>
                                                            <tr>
                                                                <td>DASH - 03254</td>
                                                                <td>0.025845845</td>
                                                                <td>-07.09% <i class="fa fa-arrow-down text-danger"></i></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                            <div id="navpills-10" class="tab-pane">
                                                <div class="table-responsive">
                                                    <table class="table table-hover text-center">
                                                        <thead>
                                                            <tr>
                                                                <th scope="col">Market</th>
                                                                <th scope="col">Price</th>
                                                                <th scope="col">Change</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <tr>
                                                                <td>LTC - 12458</td>
                                                                <td>0.002548548</td>
                                                                <td>+12.85% <i class="fa fa-arrow-up text-success"></i></td>
                                                            </tr>
                                                            <tr>
                                                                <td>LTC - 02157</td>
                                                                <td>0.025486854</td>
                                                                <td>+05.15% <i class="fa fa-arrow-up text-success"></i></td>
                                                            </tr>
                                                            <tr>
                                                                <td>LTC - 12457</td>
                                                                <td>0.025845218</td>
                                                                <td>-02.01% <i class="fa fa-arrow-down text-danger"></i></td>
                                                            </tr>
                                                            <tr>
                                                                <td>LTC - 35487</td>
                                                                <td>0.021548754</td>
                                                                <td>+06.15% <i class="fa fa-arrow-up text-success"></i></td>
                                                            </tr>
                                                            <tr>
                                                                <td>LTC - 03254</td>
                                                                <td>0.025845845</td>
                                                                <td>-07.09% <i class="fa fa-arrow-down text-danger"></i></td>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                    <!-- /.box-body -->
                                </div>
                            </div>
                            <div id="navpills-4" class="tab-pane">
                                <div class="row">
                                    <div class="col-md-6 col-lg-6 col-12">
                                        <div class="box box-inverse bg-dark3 bg-hexagons-white pull-up">
                                            <div class="box-body text-center">
                                                <h2 class="mb-0 text-bold">XRP</h2>
                                                <h4>Ripple</h4>
                                                <ul class="flexbox flex-justified text-center mt-30 bb-1 border-gray pb-20">
                                                    <li class="be-1 border-gray">
                                                        <div>USD</div>
                                                        <small class="fs-18">11.153</small>
                                                    </li>

                                                    <li class="be-1 border-gray">
                                                        <div>EUR</div>
                                                        <small class="fs-18">1.9232</small>
                                                    </li>

                                                    <li>
                                                        <div>GBP</div>
                                                        <small class="fs-18">1.8202</small>
                                                    </li>
                                                </ul>
                                                <ul class="flexbox flex-justified text-center mt-20">
                                                    <li class="be-1 border-gray">
                                                        <div>% 1h</div>
                                                        <small class="fs-18"><i class="fa fa-arrow-up text-success pe-5"></i>1.4</small>
                                                    </li>

                                                    <li class="be-1 border-gray">
                                                        <div>% 24h</div>
                                                        <small class="fs-18"><i class="fa fa-arrow-up text-success pe-5"></i>3.29</small>
                                                    </li>

                                                    <li>
                                                        <div>% 7d</div>
                                                        <small class="fs-18"><i class="fa fa-arrow-up text-success pe-5"></i>54.77</small>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6 col-lg-6 col-12">
                                        <div class="box box-inverse bg-primary bg-hexagons-dark pull-up">
                                            <div class="box-body text-center">
                                                <h2 class="mb-0 text-bold"><span>ETH</span></h2>
                                                <h4><span>Ethereum</span></h4>
                                                <ul class="flexbox flex-justified text-center mt-30 bb-1 border-light pb-20">
                                                    <li class="be-1 border-light">
                                                        <div>USD</div>
                                                        <small class="fs-18"><span>2.153</span></small>
                                                    </li>

                                                    <li class="be-1 border-light">
                                                        <div>EUR</div>
                                                        <small class="fs-18"><span>3.9232</span></small>
                                                    </li>

                                                    <li>
                                                        <div>GBP</div>
                                                        <small class="fs-18"><span>3.8202</span></small>
                                                    </li>
                                                </ul>
                                                <ul class="flexbox flex-justified text-center mt-20">
                                                    <li class="be-1 border-light">
                                                        <div>% 1h</div>
                                                        <small class="fs-18"><span><i class="fa fa-arrow-up text-success pe-5"></i>0.4</span></small>
                                                    </li>

                                                    <li class="be-1 border-light">
                                                        <div>% 24h</div>
                                                        <small class="fs-18"><span><i class="fa fa-arrow-up text-success pe-5"></i>9.29</span></small>
                                                    </li>

                                                    <li>
                                                        <div>% 7d</div>
                                                        <small class="fs-18"><span><i class="fa fa-arrow-up text-success pe-5"></i>50.77</span></small>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="navpills-5" class="tab-pane">
                                <div>
                                    <div class="table-responsive">
                                        <table class="table no-margin no-border">
                                            <thead>
                                                <tr>
                                                    <th class="text-primary">Type</th>
                                                    <th class="text-primary">Date &amp; Time</th>
                                                    <th class="text-primary">Amount</th>
                                                    <th class="text-primary">Price</th>
                                                    <th class="text-primary">Fee</th>
                                                    <th class="text-primary">Total</th>
                                                    <th class="text-primary">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="text-success">Buy</td>
                                                    <td>14-5-2019 05:11</td>
                                                    <td>1.845126523</td>
                                                    <td>5.25</td>
                                                    <td>17800.00</td>
                                                    <td>23000.00</td>
                                                    <td>Cancel</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-success">Buy</td>
                                                    <td>16-5-2019 08:11</td>
                                                    <td>2.21548756</td>
                                                    <td>0.25</td>
                                                    <td>13950.00</td>
                                                    <td>77201.00</td>
                                                    <td>Cancel</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-danger">Sell</td>
                                                    <td>12-5-2019 11:11</td>
                                                    <td>8.00200000</td>
                                                    <td>0.75</td>
                                                    <td>19555.22</td>
                                                    <td>35000.00</td>
                                                    <td>Cancel</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-danger">Sell</td>
                                                    <td>31-5-2019 12:11</td>
                                                    <td>5.2000000</td>
                                                    <td>5.25</td>
                                                    <td>18445.00</td>
                                                    <td>44521.11</td>
                                                    <td>Cancel</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-success">Buy</td>
                                                    <td>13-5-2019 14:11</td>
                                                    <td>1.845126523</td>
                                                    <td>1.25</td>
                                                    <td>17800.00</td>
                                                    <td>23000.00</td>
                                                    <td>Cancel</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-success">Buy</td>
                                                    <td>3-5-2019 17:11</td>
                                                    <td>5.2000000</td>
                                                    <td>5.25</td>
                                                    <td>18445.00</td>
                                                    <td>44521.11</td>
                                                    <td>Cancel</td>
                                                </tr>
                                                <tr>
                                                    <td class="text-danger">Sell</td>
                                                    <td>12-5-2019 11:11</td>
                                                    <td>8.00200000</td>
                                                    <td>0.75</td>
                                                    <td>19555.22</td>
                                                    <td>35000.00</td>
                                                    <td>Cancel</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>
                <div class="col-xl-3 col-12">
                    <div class="box">
                        <div class="box-body">
                            <div class="flexbox align-items-center">
                                <div>
                                    <div class=" d-flex m-0">
                                        <a href="index.html#"><i class="cc ETH-alt" title="XRP"></i></a>
                                        <div>
                                            <p class=" mb-0 mt-10 fw-700 text-fade">ETH</p>
                                            <p class=" mb-0 fw-700 fs-16">Price Statistics</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-controls text-end">
                                    <li class="dropdown">
                                        <a data-bs-toggle="dropdown" href="index.html#"><i class="fa fa-caret-down text-light me-1 fs-20"></i></a>
                                        <div class="dropdown-menu dropdown-menu-end">
                                            <a class="dropdown-item active" href="index.html#">Today</a>
                                            <a class="dropdown-item" href="index.html#">Yesterday</a>
                                            <a class="dropdown-item" href="index.html#">Last week</a>
                                            <a class="dropdown-item" href="index.html#">Last month</a>
                                        </div>
                                    </li>
                                </div>
                            </div>

                            <div class="media align-items-center p-0 justify-content-between">

                            </div>
                        </div>
                        <div class="box-body p-0">
                            <div class="table-responsive buy-sall-table">
                                <ul class="list-unstyled market-price-list">
                                    <li>
                                        <a href="index.html#" class="market-pair">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <p class="mb-0 text-light">Price</p>
                                                <p class="mb-0 text-dark fw-600">$2,850.80</p>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="index.html#" class="market-pair">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <p class="mb-0 text-light">Price Change</p>
                                                <p class="mb-0 text-success fw-600"><i class="fa fa-caret-up text-success me-1"></i> 0.80%</p>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="index.html#" class="market-pair">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <p class="mb-0 text-light">24 High</p>
                                                <p class="mb-0 text-dark fw-600">2,600.20</p>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="index.html#" class="market-pair">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <p class="mb-0 text-light">24 Low</p>
                                                <p class="mb-0 text-dark fw-600">2450.80</p>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="index.html#" class="market-pair">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <p class="mb-0 text-light">Volume</p>
                                                <p class="mb-0 text-dark fw-600">215,216,852.20</p>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="index.html#" class="market-pair">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <p class="mb-0 text-light">Dominance</p>
                                                <p class="mb-0 text-dark fw-600">18%</p>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <a href="index.html#" class="market-pair">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <p class="mb-0 text-light">Rank</p>
                                                <p class="mb-0 text-dark fw-600">#5</p>
                                            </div>
                                        </a>
                                    </li>
                                    <li>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <p class="mb-0 mx-20 text-light">Marketing Cap</p>
                                        </div>
                                        <a href="index.html#" class="market-pair  pb-2">

                                            <div class="d-flex justify-content-between align-items-center">
                                                <p class="mb-0 text-dark fw-600">#258,456,789.20</p>
                                                <p class="mb-0 text-danger fw-600"><i class="fa fa-caret-down text-danger me-1"></i> 0.40%</p>
                                            </div>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
        </section>
        <!-- /.content -->
    </div>
</div>
@endsection