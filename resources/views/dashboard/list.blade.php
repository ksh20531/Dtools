<style type="text/css">
	/*.row{
		cursor: pointer;
	}*/
</style>
<table class="table">
	<tr>
		<th>id</th>
		<th>title</th>
	</tr>
	@foreach($dashboards as $dashboard)
	<tr class="">
		<td width="50px">{{ $dashboard->id }}</td>
		<td style="cursor: pointer;" onclick="openDashboard({{ $dashboard->id }})">{{ $dashboard->title }}</td>
	</tr>
	@endforeach
</table>
<div class="pagination">{{ $dashboards->links('pagination::bootstrap-4') }}</div>

<script type="text/javascript">
	function openDashboard(id){
		window.open('/dashboard/'+id)
	}
</script>