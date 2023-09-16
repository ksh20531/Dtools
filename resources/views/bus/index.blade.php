@extends('layouts.app')

@section('style')
<style type="text/css">
	.modal-body{
		height: 500px;
		overflow: scroll;
	}
	.modal-bus-list-area{
		display: block;
	}
	.modal-bus-list{
		display: inline-block;
		cursor: pointer;
	}
	.modal-book-mark{
		cursor: pointer;
	}
	.info-area{
		margin: 10px 0px 10px 0px;
		height: 50px;
		border: 1px solid black;
	}
	.select-area{
		height: 80vh;
	}
	.bus-select-area{
		float: left;
		width: calc(50% - 5px);
		height: 80vh;
		border: 1px solid black;
	}
	.bus-stop-select-area{
		float: right;
		width: calc(50% - 5px);
		height: 80vh;
		border: 1px solid black;
	}
	.search-area{
		height: 50px;
	}
	.search{
		display: inline-block;
		width: calc(100% - 100px);
		height: 32px;
		margin: 6px 0px 0px 5px;
	}
	.bus-list-area{
		height: calc(80vh - 250px);
		width: 100%;
		padding: 0px 0px 0px 5px;
		margin: 0px 0px 0px 0px;
		overflow: scroll !important;
	}
	.book-mark-area{
		height: 190px;
		width: 100%;
		padding: 0px 0px 0px 5px;
		border-top: 1px solid black;
		overflow: scroll !important;
	}
	.route-list-area{
		height: 80vh;
		width: 100%;
		margin: 0px 0px 0px 5px;
		overflow: scroll !important;
	}
	.bus-list-item{
		cursor: pointer;
	}
	.route-list-item{
		cursor: pointer;
	}
</style>
@endsection

@component('components.modal', [
    'id'    => 'modal-id',
    'class' => 'additional classes',
])
    @slot('title','Bus List')

    @slot('body')

    @endslot

    @slot('footer')
		<button class="btn btn-danger btn-sm" onclick="closeModal()">close</button>
    @endslot
@endcomponent

@section('content')
<div class="info-area"></div>
<div class="select-area">
	<div class="bus-select-area">
		<div class="search-area">
			<input class="search" placeholder="Bus Number">
			<button class="btn btn-primary btn-sm" onclick="searchBus()">search</button>
		</div>
		<div class="bus-list-area"></div>
		<div class="book-mark-area"></div>
	</div>
	<div class="bus-stop-select-area">
		<div class="route-list-area"></div>
	</div>
</div>
@endsection

@section('script')
<script type="text/javascript">
	$(function(){
		getBookMark();
    	$('.modal').modal({backdrop: 'static'});
		$(".search").keypress(function(e){
			if(e.keyCode && e.keyCode == 13){
				searchBus();
			}
		});
	});

	function searchBus(url = "api/searchBus"){ 
		var busStr = $(".search").val();

		ajax_data = {
			busStr : busStr,
			user_id : {{ Auth::user()->id }},
		};

		$.ajax({    
			type : 'get',
			url : url,
			data : ajax_data,    
			success : function(result) {
				$('.modal-body').empty();
				openModal();

				if(result.itemList == undefined || result.itemList == null){
					$('.modal-body').html("검색된 결과가 없습니다.");
				}else{
					if(result.itemList.busCount == 1){
						var bus = result.itemList;
					
						if(bus.is_marked == 1)
							var bookMark = "fa-solid";
						else
							var bookMark = "fa-regular";

						$('.modal-body').append("<div class='modal-bus-list-area'><div class='modal-bus-list' onclick='getBus(this,"+bus.busRouteId+")'>"+bus.busRouteNm+"</div> <i class='"+bookMark+" fa-star modal-book-mark' onclick='bookMark(this,"+bus.busRouteId+",\""+bus.busRouteNm+"\")'></i></div>");
					}else{
						result.itemList.forEach(function (bus,idx){
							if(bus.is_marked == 1)
								var bookMark = "fa-solid";
							else
								var bookMark = "fa-regular";

							$('.modal-body').append("<div class='modal-bus-list-area'><div class='modal-bus-list' onclick='getBus(this,"+bus.busRouteId+")'>"+bus.busRouteNm+"</div> <i class='"+bookMark+" fa-star modal-book-mark' onclick='bookMark(this,"+bus.busRouteId+",\""+bus.busRouteNm+"\")'></i></div>");
						});
					}
				}
			},    
			error : function(request, status, error) {
				console.log(error)    
			}
		});
	}

	function getBus(elem,busRouteId){
		$('.bus-list-area').empty();

		var url = "api/getBus";
		var busStr = $(".search").val();

		ajax_data = {
			busRouteId: busRouteId,
		};

		$.ajax({    
			type : 'get',
			url : url,
			data : ajax_data,    
			success : function(result) {
				$(".search").val($(elem).text());
				$(".modal").modal("hide");

				if(result.itemList == undefined || result.itemList == null){
					$('.bus-list-area').html("운행중인 버스가 없습니다.");
				}else{
					if(result.itemList.busCount == 1){
						var bus = result.itemList;
						var plainNo = bus.plainNo.slice(-4,bus.plainNo.length);
						$('.bus-list-area').append("<div class='bus-list-item' onclick='selectBus("+busRouteId+","+plainNo+")'>"+plainNo+" -> "+bus.stationNm+"</div>");
					}else{
						result.itemList.forEach(function (bus,idx){
							var plainNo = bus.plainNo.slice(-4,bus.plainNo.length);
							$('.bus-list-area').append("<div class='bus-list-item' onclick='selectBus("+busRouteId+","+plainNo+")'>"+plainNo+" -> "+bus.stationNm+"</div>");
						});
					}
				}
			},    
			error : function(request, status, error) {
				console.log(error)    
			}
		});
	}
	
	function selectBus(busRouteId,selectedBusRouteId){
		$('.route-list-area').empty();

		var url = "api/selectBus";
		ajax_data = {
			busRouteId: busRouteId,
			selectedBusRouteId: selectedBusRouteId,
		};

		$.ajax({    
			type : 'get',
			url : url,
			data : ajax_data,    
			success : function(result) {
				result.itemList.forEach (function (route, idx){
					$('.route-list-area').append("<div class='route-list-item' onclick='selectStation("+route.station+","+route.seq+","+route.busRouteId+","+selectedBusRouteId+")'>"+route.stationNm+"</div>");
				});
			},    
			error : function(request, status, error) {
				console.log(error)    
			}
		});
	}

	function selectStation(stationId,ord,busRouteId,selectedBusNumber){
		var timer = 15;

		var url = "api/selectStation";
		ajax_data = {
			busStr: $(".search").val(),
			stationId: stationId,
			ord: ord,
			busRouteId: busRouteId,
			selectedBusNumber: selectedBusNumber,
		};

		var ajaxCall = function(){
			$.ajax({    
				type : 'get',
				url : url,
				data : ajax_data,    
				success : function(result) {
					var stationNm = result['stationNm'];
					var arrMsg = result['arrMsg'];
					var leftStations = result['leftStations'];
					if(arrMsg == null || arrMsg == '')
						$('.info-area').html('선택한 버스 : '+selectedBusNumber+" / 선택한 정류장 : "+stationNm+"<br>도착 정보 : "+leftStations+" 정거장 전");
					else
						$('.info-area').html('선택한 버스 : '+selectedBusNumber+" / 선택한 정류장 : "+stationNm+"<br>도착 정보 : "+arrMsg);
					if(result['arrMsg'].indexOf('도착') > -1){
						//ringing, need web push
						var audio = new Audio("{{ asset('alarm_1.mp3') }}");
						audio.volume = 0.1;
						audio.play();
					}
				},    
				error : function(request, status, error) {
					console.log(error)    
				}
			});
		};
		ajaxCall();
		var interval = setInterval(ajaxCall,timer * 1000);
	}

	function getBookMark(){
		$('.book-mark-area').empty();
		var url = 'api/getBookMark';

		ajax_data = {
			user_id : {{ Auth::user()->id }},
		};

		$.ajax({    
			type : 'get',
			url : url,
			data : ajax_data,    
			success : function(result) {
				result.forEach(function(bus,idx){
					if(bus.is_marked == 1)
						var bookMark = "fa-solid";
					else
						var bookMark = "fa-regular";

					var busRouteNm = bus.bus_route_name.toString();

					$('.book-mark-area').append("<div class='modal-bus-list-area'><div class='modal-bus-list' onclick='getBus(this,"+bus.bus_route_id+")'>"+busRouteNm+"</div>  <i class='"+bookMark+" fa-star' onclick='bookMark(this,"+bus.bus_route_id+",\""+busRouteNm+"\")'></i></div>")
				});
			},    
			error : function(request, status, error) {
				console.log(error)    
			}
		});
	}

	function bookMark(elem,busRouteId,busRouteNm){
		var is_marked = 0;
		if($(elem).hasClass('fa-regular')){
			$(elem).removeClass('fa-regular');
			$(elem).addClass('fa-solid');
			is_marked = 1;
		}else{
			$(elem).addClass('fa-regular');
			$(elem).removeClass('fa-solid');
			deleteBookMark(busRouteId,busRouteNm);
			return;
		}

		var url = "api/bookMark";
		ajax_data = {
			is_marked : is_marked,
			busRouteId : busRouteId,
			busRouteNm : busRouteNm,
			user_id : {{ Auth::user()->id }},
		};

		$.ajax({    
			type : 'put',
			url : url,
			data : ajax_data,    
			success : function(result) {
				if(result == 'success')
					$('.book-mark-area').append("<div class='modal-bus-list-area'><div class='modal-bus-list' onclick='getBus(this,"+busRouteId+")'>"+busRouteNm+"</div>  <i class='fa-solid fa-star' onclick='deleteBookMark("+busRouteId+",\""+busRouteNm+"\")'></i></div>")
			},    
			error : function(request, status, error) {
				console.log(error)    
			}
		});
	}

	function deleteBookMark(busRouteId,busRouteNm){
		var url = "api/deleteBookMark";
		ajax_data = {
			busRouteId : busRouteId,
			busRouteNm : busRouteNm,
			user_id : {{ Auth::user()->id }},
		};

		$.ajax({    
			type : 'delete',
			url : url,
			data : ajax_data,    
			success : function(result) {
				if(result == 'success')
					getBookMark();
			},    
			error : function(request, status, error) {
				console.log(error)    
			}
		});
	}

	

	function openModal(){
    	$(".modal").modal("show");
	}

	function closeModal(){
		$('.modal-body').empty();
		$(".modal").modal("hide");
	}

</script>
@endsection
