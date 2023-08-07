<style type="text/css">
	.tool{
		cursor: pointer;
	}
</style>

<table class="table">
	<tr>
		<th>id</th>
		<th>title</th>
	</tr>
	@foreach($menus as $menu)
	<tr>
		<td width="50px">{{ $menu->id }}</td>

		<td class="tool" onclick="openTool('{{ $menu->url }}')">{{ $menu->title }}</td>
	</tr>
	@endforeach
</table>
<div class="pagination">{{ $menus->links('pagination::bootstrap-4') }}</div>

<script type="text/javascript">
	function openTool(url){
		window.open(url);
	}