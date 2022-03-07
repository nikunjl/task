@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
        	<a class="btn btn-success" href="{{ route('product.create') }}">Create New Product</a>
            <a class="btn btn-success" href="{{ route('importView') }}">Import</a>
            <a class="btn btn-success" href="{{ route('exportProduct') }}">Export</a>
        	<hr>
            <h4>Filter</h4>
            <hr>
            <form method="get" action="{{ route('product.index')}}">
                <div class="row mb-3">
                    <label for="price" class="col-md-4 col-form-label text-md-end">Price</label>
                    <div class="col-md-2">
                        <input id="price" type="number" class="form-control" name="filter[min]" value="{{ isset($filter['min']) ? $filter['min'] : '' }}" autocomplete="off">
                    </div>
                    <div class="col-md-2">
                        <input id="price" type="number" class="form-control" name="filter[max]" value="{{ isset($filter['max']) ? $filter['max'] : '' }}" autocomplete="off">
                    </div>                    
                </div>

                <div class="row mb-3">
                    <label for="stock" class="col-md-4 col-form-label text-md-end">Stock</label>
                    <div class="col-md-6">
                        <select name="filter[stock]" class="form-control">
                            <option value="">Select</option>
                            <option value="{{ 'yes' }}" @if(isset($filter['stock']) && $filter['stock'] == 'yes') selected="selected" @endif>{{ 'Yes' }}</option>
                            <option value="{{ 'no' }}" @if(isset($filter['stock']) && $filter['stock'] == 'no') selected="selected" @endif>{{ 'No' }}</option>
                        </select>
                    </div>
                </div>

                <div class="row mb-0">
                    <div class="col-md-8 offset-md-4">
                        <button type="submit" class="btn btn-primary">
                            {{ __('Search') }}
                        </button>
                        <a href="{{ route('product.index') }}" class="btn btn-danger">Cancel</a>    
                    </div>
                </div>
            </form>
            <div class="card">
                <div class="card-header">{{ __('Product List') }}</div>
                <div class="card-body">
                    <table class="table">
                    	<tr>
                    		<th>Sr.no</th>
                    		<th>Product Name</th>
                    		<th>Price</th>
                    		<th>Stock</th>
                            <th>Shop Name</th>                            
                            <th>Status</th>
                    		<th>Action</th>
                    	</tr>

                    	@if(!empty($products))
                    		@foreach($products as $k => $product)
                    			<tr>
                    				<td>{{ $k+1 }}</td>
                    				<td>{{ $product->product_name }}</td>
                    				<td>{{ $product->price }}</td>
                    				<td>{{ $product->stock }}</td>
                                    <td>{{ $product->shops->shop_name }}</td>
                                    <td>
                                        @if($product->status == 1)
                                            <span class="btn btn-success">{{ 'Active' }}</span>
                                        @else
                                            <span class="btn btn-danger">{{ 'Deactive' }}</span>
                                        @endif
                                    </td>
                    				<td>
                    					<a class="btn btn-primary" href="{{ route('product.edit',array($product->id)) }}">Edit</a> |
                                        <form class="" action="{{ url('product/destroy') }}" method="post">
                                            <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure?')">Delete</button>
                                          <input type="hidden" name="id" value="{{$product->id}}">
                                          {{ method_field('DELETE') }}
                                          {!! csrf_field() !!}
                                        </form>
                    				</td>
                    			</tr>
                    		@endforeach
                    	@endif
                    </table>

                    {{ $products->links() }}
                </div>
            </div>
        </div>
    </div>
</div>

@endsection