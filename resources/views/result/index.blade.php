@extends('layouts.app')

@section('style')
<style type="text/css">
	.content{
		display: grid;
		grid-template-rows: 50px auto;
		column-gap: 100px;
	}
	.item:nth-child(1){
		grid-column-start: 1;
		grid-column-end: 4;
		grid-row-start: 1;
		grid-row-end: 1;
		margin-top: 10px;
	}
	.item:nth-child(2){
		width: 600px;
		grid-column-start: 1;
		grid-column-end: 3;
		grid-row-start: 2;
		grid-row-end: 5;
	}
	.item:nth-child(3){
		height: 250px;
		grid-column-start: 3;
		grid-column-end: 4;
		grid-row-start: 2;
		grid-row-end: 3;
	}
	.item:nth-child(4){
		height: 250px;
		grid-column-start: 3;
		grid-column-end: 4;
		grid-row-start: 3;
		grid-row-end: 4;
	}
	.item:nth-child(5){
		height: 250px;
		grid-column-start: 3;
		grid-column-end: 4;
		grid-row-start: 4;
		grid-row-end: 5;
	}
</style>
@endsection

@section('content')
<div class="content">
	<div class="item">
		<input type="text" id="daterange" name="daterange"/ style="width: 210px;">
	</div>
	<div class="item total">
		<canvas id="total"></canvas>
	</div>
	<div class="item sulak">
		<canvas id="sulak"></canvas>
	</div>
	<div class="item iljuk">
		<canvas id="iljuk"></canvas>
	</div>
	<div class="item yeoju">
		<canvas id="yeoju"></canvas>
	</div>
</div>
@endsection

@section('script')
<script type="text/javascript">
	$(function(){
		$('#daterange').daterangepicker({
			startDate: moment().startOf('month').format('YYYY-M-DD'),
			endDate: moment().endOf('month').format('YYYY-M-DD'),
			locale: {
				format: 'YYYY-M-DD',
				separator : " ~ ",
				applyLabel: '확인',
				cancelLabel: "취소",
			}
		});
		$('#daterange').on('apply.daterangepicker', function(ev, picker) {
			getData();
		});
		getData();
	});

	function getData(url = "/api/results"){
		var start = $("#daterange").data('daterangepicker').startDate.format("YYYY-MM-DD").toString();
		var end = $("#daterange").data('daterangepicker').endDate.format("YYYY-MM-DD").toString();

		ajax_data = {
			start : start,
			end : end
		};

		$.ajax({    
			type : 'get',
			url : url,
			data : ajax_data,    
			success : function(result) {
				var sulak = $('#sulak');
				var iljuk = $('#iljuk');
				var yeoju = $('#yeoju');
				var total = $('#total');

				var fields = ['total','sulak','iljuk','yeoju'];
				var names = ['종합','설악','일죽','여주'];

				fields.forEach(function(elem,idx){
					var canvas = $("canvas")[idx];
					var canvas_p = $(canvas).parent();

					$(canvas).remove();
					$(canvas_p).append(`<canvas id="${fields[idx]}"></canvas>`);

					var percent = (result[elem]['success'] / result[elem]['count'] * 100).toFixed(1);
					if(isNaN(percent))
						percent = 0;
					var text = " " + percent + "%";

					if(elem == 'total'){
						var labels = ['설악','일죽','여주','실패'];
						var data = [
								result['sulak']['success'],
								result['iljuk']['success'],
								result['yeoju']['success'],
								result['total']['count']-result['total']['success']
							];
					}else{
						var labels = ['성공','실패'];
						var data = [
								result[elem]['success'],
								result[elem]['count']-result[elem]['success']
							];
					}

					new Chart(elem, {
						type: 'pie',
						data: {
							labels: labels,
							datasets: [{
								data: data,
								borderWidth: 0,
							}]
						},
						options: {
							maintainAspectRatio: false,
							onClick: function(point,event){
								// onClick 이벤트 추가 가능.
							},
							plugins: {
								legend: {
									position: 'top',
									labels: {
										font: {size: 18}
									}
								},
								title: {
									display: true,
									text: names[idx]+text,
									font: {size: 25}
								},
								tooltip: {
									callbacks: {
										label: function(context) {
											return context.parsed+"건";
										}

									}										
								}
							}
						}
					});
				});
			},    
			error : function(request, status, error) {
				console.log(error)    
			}
		})
	}
</script>
@endsection
