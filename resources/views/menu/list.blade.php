<style type="text/css">
	.tool{
		cursor: pointer;
		width: 300px;
	}
	.table{
		margin: 10px 0px 5px 0px;
	}
</style>

<table class="table">
	<tr>
		<th>ID</th>
		<th>Title</th>
		<th>Description</th>
	</tr>
	@foreach($menus as $menu)
	<tr>
		<td width="50px">{{ $menu->id }}</td>
		<td class="tool" onclick="openTool('{{ $menu->url }}')">{{ $menu->title }}</td>
		<td>{{ $menu->description }}</td>
	</tr>
	@endforeach
</table>
<div class="pagination">{{ $menus->links('pagination::bootstrap-4') }}</div>

<script type="text/javascript">
	function openTool(url){
		window.open(url);
	}