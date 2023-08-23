@extends('layouts.app')

@section('style')
<style type="text/css">

</style>

@component('components.modal', [
    'id'    => 'modal-id',
    'class' => 'additional classes',
])
    @slot('title', 'modal title')

    @slot('body')
		modal body
    @endslot

    @slot('footer')
    	<button class="btn btn-primary btn-sm">submit</button>
		<button class="btn btn-danger btn-sm" onclick="closeModal()">close</button>
    @endslot
@endcomponent

@section('content')
<div class="routine-header">
	<button class="btn btn-primary btn-sm" onclick="addRoutine()">추가</button>
	<button class="btn btn-primary btn-sm" onclick="openModal()">modal</button>
</div>
<div class="routine-container"></div>
@endsection

@section('script')
<script type="text/javascript">
    $(function(){

    });

    function openModal() {
    	$(".modal").modal("show");
    }

    function closeModal(){
    	$(".modal").modal("hide");
    }
</script>
@endsection
