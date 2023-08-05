<table class="table">
	<tr>
		<th>id</th>
		<th>title</th>
	</tr>
	@foreach($menus as $menu)
	<tr>
		<td width="50px">{{ $menu->id }}</td>
		<td><a href="#">{{ $menu->title }}</td>
	</tr>
	@endforeach
</table>
<div class="pagination">{!! $menus->links('pagination::bootstrap-4')  !!}</div>