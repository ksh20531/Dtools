<style type="text/css">
	
</style>
<table class="table">
	<tr>
		<th>ID</th>
		<th>Title</th>
		<th>User</th>
		<th>Created at</th>
	</tr>
	@foreach($dashboards as $dashboard)
	<tr class="">
		<td width="50px">{{ $dashboard->dashboard_id }}</td>
		<td style="cursor: pointer;" onclick="openDashboard({{ $dashboard->dashboard_id }})">{{ $dashboard->title }}</td>
		<td width="250px">{{ $dashboard->email }}</td>
		<td width="250px">{{ $dashboard->dashboard_created_at }}</td>
	</tr>
	@endforeach
</table>
<div class="pagination">{{ $dashboards->links('pagination::bootstrap-4') }}</div>

<script type="text/javascript">
	function openDashboard(id){
		window.open('/dashboard/'+id)
	}
</script>