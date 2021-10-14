@extends('restaurant.app2')

@section('content')
<div class="container mx-auto food-set">

    {{-- ADDING FOOD SETS ALERT --}}
    @if (session()->has('addedFoodSet'))
        <script>
            Swal.fire(
                'Food Set added to Order Set',
                '',
                'success'
            );
        </script>
    @endif
    @if (session()->has('someAreExistingFoodSet'))
        <script>
            Swal.fire(
                'Food Set added to Order Set',
                'but some food set are not added because it is already exist',
                'success'
            );
        </script>
    @endif
    @if (session()->has('allExistingFoodSet'))
        <script>
            Swal.fire(
                'Food Set is already exist in this Order Set',
                '',
                'warning'
            );
        </script>
    @endif
    @if (session()->has('emptyFoodSet'))
        <script>
            Swal.fire(
                'No Set Added',
                'Please select Food Set',
                'error'
            );
        </script>
    @endif

    {{-- ADDING FOOD ITEMS ALERT --}}
    @if (session()->has('emptyFoodItem'))
    <script>
        Swal.fire(
            'Please add food item first',
            '',
            'error'
        );
    </script>
@endif
    @if (session()->has('addedFoodItem'))
        <script>
            Swal.fire(
                'Food item added to order set',
                '',
                'success'
            );
        </script>
    @endif
    @if (session()->has('allExistingFoodItem'))
        <script>
            Swal.fire(
                'All items already exist in food item',
                '',
                'error'
            );
        </script>
    @endif
    @if (session()->has('allExistingFoodSets'))
        <script>
            Swal.fire(
                'All item already exist in food set',
                '',
                'warning'
            );
        </script>
    @endif
    @if (session()->has('someExistInFoodItem'))
        <script>
            Swal.fire(
                'Item Added',
                'some item are already exist in food items',
                'success'
            );
        </script>
    @endif
    @if (session()->has('someExistInFoodSet'))
        <script>
            Swal.fire(
                'Item Added',
                'some item are already exist in food sets',
                'success'
            );
        </script>
    @endif
    @if (session()->has('allExistInFoodSetAndFoodItem'))
        <script>
            Swal.fire(
                'Items are already existing in food items and food sets',
                '',
                'error'
            );
        </script>
    @endif
    @if (session()->has('someExistInFoodSetAndFoodItem'))
        <script>
            Swal.fire(
                'Item Added',
                'some item are already exist in both food sets and food item',
                'success'
            );
        </script>
    @endif

    {{-- DELETING FOOD SETS AND FOOD ITEMS --}}
    @if (session()->has('deletedFoodItem'))
        <script>
            Swal.fire(
                'Food Item Deleted in Order Set',
                '',
                'success'
            );
        </script>
    @endif
    @if (session()->has('deletedFoodSet'))
        <script>
            Swal.fire(
                'Food Set Deleted in Order Set',
                '',
                'success'
            );
        </script>
    @endif
    
    <div class="w-11/12 mx-auto mt-10">
        <a href="/restaurant/manage-restaurant/food-menu/order-set" class="mt-2 text-submitButton uppercase font-bold"><i class="fas fa-chevron-left mr-2"></i>Back</a>
        <div class="w-full mt-3 text-right">
            <a href="/restaurant/manage-restaurant/food-menu/order-set/detail/edit/{{ $orderSet->id }}" class="text-white bg-manageRestaurantSidebarColorActive inline-block text-center leading-9 w-9 h-9 rounded-full mr-3"><i class="fas fa-edit"></i></a>
            <a href="/restaurant/manage-restaurant/food-menu/order-set/delete/{{ $orderSet->id }}" class="btn-delete text-white bg-red-800 inline-block text-center leading-9 w-9 h-9 rounded-full"><i class="fas fa-trash-alt"></i></a>
        </div>
        
        <div class="w-full mt-3 rounded-2xl shadow-adminDownloadButton">
            <div class="rounded-t-2xl bg-manageRestaurantSidebarColorActive text-white font-bold text-center w-full h-11 grid items-center">Order Set</div>
            <div class="rounded-b-xl grid grid-cols-foodSet px-4 bg-adminViewAccountHeaderColor2 py-7">
                <div class="self-center">
                    <img src="{{ asset('uploads/restaurantAccounts/orderSet/'.$id.'/'.$orderSet->orderSetImage) }}" alt="orderSetImage" class="w-40 h-40">
                </div>
                <div class="mt-4">
                    <div class="flex justify-between">
                        <p class="text-4xl uppercase font-bold">{{ $orderSet->orderSetName }}</p>
                        <p class="text-lg font-bold text-manageRestaurantSidebarColor">Price: <span class="text-red-800"> Php {{ $orderSet->orderSetPrice }} </span></p>
                    </div>
                    <p class="mt-3 text-sm">{{ $orderSet->orderSetDescription }}</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-5 w-full">
            {{-- FOOD SETS CHOICES --}}
            <div class="col-span-1">
                <div class="col-span-1">
                    <div class="w-full mt-10 mb-12 rounded-2xl shadow-adminDownloadButton bg-adminViewAccountHeaderColor2">
                        <div class="rounded-t-2xl bg-manageRestaurantSidebarColorActive w-full flex justify-between items-center px-4 py-3">
                            <div class="text-white font-bold uppercase text-xl">Food Sets</div>
                            <div class="">
                                <button id="btn-add-food-set" class="bg-submitButton text-white w-36 h-9 rounded-md"><i class="fas fa-plus mr-3"></i>Food Set</button>
                            </div>
                        </div>
                        <div class="grid grid-cols-11 items-center text-center font-bold h-16 px-5">
                            <div class="col-span-1">Image</div>
                            <div class="col-span-1">Id</div>
                            <div class="col-span-2">Name</div>
                            <div class="col-span-5">Description</div>
                            <div class="col-span-1">Price</div>
                            <div class="col-span-1">Delete</div>
                        </div>
                        @if (!$orderSetFoodSets->isEmpty())
                            @php
                                $count = 1
                            @endphp
                            @foreach ($orderSetFoodSets as $orderSetFoodSet)  
                                @foreach ($foodSets as $foodSet)
                                    @if ($orderSetFoodSet->foodSet_id == $foodSet->id)
                                        @if ($count % 2 == 0)
                                            <div class="bg-manageFoodItemHeaderBgColor grid grid-cols-11 justify-items-center items-center h-10 px-5 mb-2">
                                                <div class="col-span-1">
                                                    <img src="{{ asset('uploads/restaurantAccounts/foodSet/'.$id.'/'.$foodSet->foodSetImage) }}" alt="foodSetImage" class="w-8 h-7">
                                                </div>
                                                <div class="col-span-1">{{ $foodSet->id }}</div>
                                                <div class="col-span-2">{{ $foodSet->foodSetName }}</div>
                                                <div class="col-span-5">{{ $foodSet->foodSetDescription }}</div>
                                                <div class="col-span-1">{{ $foodSet->foodSetPrice }}</div>
                                                <div class="col-span-1">
                                                    <a href="/restaurant/manage-restaurant/food-menu/order-set/food-set/delete/{{ $orderSet->id }}/{{ $orderSetFoodSet->id }}" class="btn-delete2"><i class="fas fa-trash-alt"></i></a>
                                                </div>
                                            </div>
                                        @else
                                            <div class="bg-white grid grid-cols-11 justify-items-center items-center h-10 px-5 mb-2">
                                                <div class="col-span-1">
                                                    <img src="{{ asset('uploads/restaurantAccounts/foodSet/'.$id.'/'.$foodSet->foodSetImage) }}" alt="foodSetImage" class="w-8 h-7">
                                                </div>
                                                <div class="col-span-1">{{ $foodSet->id }}</div>
                                                <div class="col-span-2">{{ $foodSet->foodSetName }}</div>
                                                <div class="col-span-5">{{ $foodSet->foodSetDescription }}</div>
                                                <div class="col-span-1">{{ $foodSet->foodSetPrice }}</div>
                                                <div class="col-span-1">
                                                    <a href="/restaurant/manage-restaurant/food-menu/order-set/food-set/delete/{{ $orderSet->id }}/{{ $orderSetFoodSet->id }}" class="btn-delete2"><i class="fas fa-trash-alt"></i></a>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                                @php
                                    $count++
                                @endphp
                            @endforeach
                        @endif
        
                        @if ($orderSetFoodSets->isEmpty())
                            <div class="mx-auto w-full mt-2 grid grid-cols-1 items-center bg-red-400 h-14 shadow-xl rounded-b-lg">
                                <div class="text-center text-white">There are no Food Sets in this Order Set right now</div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- FOOD ITEMS CHOICES --}}
            <div class="col-span-1">
                <div class="w-full mt-10 mb-12 rounded-2xl shadow-adminDownloadButton bg-adminViewAccountHeaderColor2">
                    <div class="rounded-t-2xl bg-manageRestaurantSidebarColorActive w-full flex justify-between items-center px-4 py-3">
                        <div class="text-white font-bold uppercase text-xl">Food Items</div>
                        <div class="">
                            <button id="btn-add-food-item" class="bg-submitButton text-white w-36 h-9 rounded-md"><i class="fas fa-plus mr-3"></i>Food Item</button>
                        </div>
                    </div>
                    <div class="grid grid-cols-11 items-center text-center font-bold h-16 px-5">
                        <div class="col-span-1">Image</div>
                        <div class="col-span-1">Id</div>
                        <div class="col-span-2">Name</div>
                        <div class="col-span-5">Description</div>
                        <div class="col-span-1">Price</div>
                        <div class="col-span-1">Delete</div>
                    </div>
                    @if (!$orderSetFoodItems->isEmpty())
                            @php
                                $count = 1
                            @endphp
                            @foreach ($orderSetFoodItems as $orderSetFoodItem)  
                                @foreach ($foodItems as $foodItem)
                                    @if ($orderSetFoodItem->foodItem_id == $foodItem->id)
                                        @if ($count % 2 == 0)
                                            <div class="bg-manageFoodItemHeaderBgColor grid grid-cols-11 justify-items-center items-center h-10 px-5 mb-2">
                                                <div class="col-span-1">
                                                    <img src="{{ asset('uploads/restaurantAccounts/foodItem/'.$id.'/'.$foodItem->foodItemImage) }}" alt="foodItemImage" class="w-8 h-7">
                                                </div>
                                                <div class="col-span-1">{{ $foodItem->id }}</div>
                                                <div class="col-span-2">{{ $foodItem->foodItemName }}</div>
                                                <div class="col-span-5">{{ $foodItem->foodItemDescription }}</div>
                                                <div class="col-span-1">{{ $foodItem->foodItemPrice }}</div>
                                                <div class="col-span-1">
                                                    <a href="/restaurant/manage-restaurant/food-menu/order-set/food-item/delete/{{ $orderSet->id }}/{{ $orderSetFoodItem->id }}" class="btn-delete3"><i class="fas fa-trash-alt"></i></a>
                                                </div>
                                            </div>
                                        @else
                                            <div class="bg-white grid grid-cols-11 justify-items-center items-center h-10 px-5 mb-2">
                                                <div class="col-span-1">
                                                    <img src="{{ asset('uploads/restaurantAccounts/foodItem/'.$id.'/'.$foodItem->foodItemImage) }}" alt="foodItemImage" class="w-8 h-7">
                                                </div>
                                                <div class="col-span-1">{{ $foodItem->id }}</div>
                                                <div class="col-span-2">{{ $foodItem->foodItemName }}</div>
                                                <div class="col-span-5">{{ $foodItem->foodItemDescription }}</div>
                                                <div class="col-span-1">{{ $foodItem->foodItemPrice }}</div>
                                                <div class="col-span-1">
                                                    <a href="/restaurant/manage-restaurant/food-menu/order-set/food-item/delete/{{ $orderSet->id }}/{{ $orderSetFoodItem->id }}" class="btn-delete3"><i class="fas fa-trash-alt"></i></a>
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                                @php
                                    $count++
                                @endphp
                            @endforeach
                        @endif
        
                        @if ($orderSetFoodItems->isEmpty())
                            <div class="mx-auto w-full mt-2 grid grid-cols-1 items-center bg-red-400 h-14 shadow-xl rounded-b-lg">
                                <div class="text-center text-white">There are no Food Items in this Order Set right now</div>
                            </div>
                        @endif
                </div>
            </div>

        </div>

        
    </div>

    {{-- FOOOOOD SETSSSSSS INVERNTORY --}}
    <div class="add-form2" id="add-form2">
        <form action="/restaurant/manage-restaurant/food-menu/order-set/detail/add-set/{{ $orderSet->id }}" method="POST" enctype="multipart/form-data">
            <div class="flex justify-between">
                <h1 class="font-bold text-xl text-white">FOOD SET INVENTORY</h1>
                <button class="bg-submitButton text-white rounded-md w-28 h-10 text-sm" type="submit">Add</button>
            </div>
            @csrf
            <div class="w-full bg-adminViewAccountHeaderColor2 mt-3">
                <div class="grid grid-cols-12 items-center text-center font-bold h-16 px-5">
                    <div class="col-span-2 bg-white py-2">
                        <input type="checkbox" name="select-all-food-set" id="select-all-food-set" class="mr-4"> 
                        <label class="text-manageRestaurantSidebarColorActive">Select All</label>
                    </div>
                    <div class="col-span-1"> </div>
                    <div class="col-span-1">Id</div>
                    <div class="col-span-2">Name</div>
                    <div class="col-span-5">Description</div>
                    <div class="col-span-1">Price</div>
                </div>
                @if (!$foodSets->isEmpty())
                    @php
                        $count = 1
                    @endphp
                    @foreach ($foodSets as $foodSet)  
                        @if ($count % 2 == 0)
                            <div class="bg-manageFoodItemHeaderBgColor grid grid-cols-12 justify-items-center items-center h-10 px-5 mb-2">
                                <div class="col-span-2">
                                    <input type="checkbox" class="foodSet" name="foodSet[]" value="{{ $foodSet->id }}">
                                </div>
                                <div class="col-span-1">
                                    <span class="hidden">{{ $foodSet->foodSetImage }}</span>
                                    <img src="{{ asset('uploads/restaurantAccounts/foodSet/'.$id.'/'.$foodSet->foodSetImage) }}" alt="foodSetImage" class="w-8 h-7">
                                </div>
                                <div class="col-span-1">{{ $foodSet->id }}</div>
                                <div class="col-span-2">{{ $foodSet->foodSetName }}</div>
                                <div class="col-span-5">{{ $foodSet->foodSetDescription }}</div>
                                <div class="col-span-1">{{ $foodSet->foodSetPrice }}</div>
                            </div>
                        @else
                            <div class="bg-white grid grid-cols-12 justify-items-center items-center h-10 px-5 mb-2">
                                <div class="col-span-2">
                                    <input type="checkbox" class="foodSet" name="foodSet[]" value="{{ $foodSet->id }}">
                                </div>
                                <div class="col-span-1">
                                    <span class="hidden">{{ $foodSet->foodSetImage }}</span>
                                    <img src="{{ asset('uploads/restaurantAccounts/foodSet/'.$id.'/'.$foodSet->foodSetImage) }}" alt="foodSetImage" class="w-8 h-7">
                                </div>
                                <div class="col-span-1">{{ $foodSet->id }}</div>
                                <div class="col-span-2">{{ $foodSet->foodSetName }}</div>
                                <div class="col-span-5">{{ $foodSet->foodSetDescription }}</div>
                                <div class="col-span-1">{{ $foodSet->foodSetPrice }}</div>
                            </div>
                        @endif
                        @php
                            $count++
                        @endphp
                    @endforeach
                @endif

                @if ($foodSets->isEmpty())
                    <div class="mx-auto w-full mt-2 grid grid-cols-1 items-center bg-red-400 h-14 shadow-xl rounded-lg">
                        <div class="text-center text-white">There are no Food Set right now</div>
                    </div>
                @endif
            </div>
        </form>
    </div>


    {{-- FOOOOOD ITEMMMMMM INVENTORY --}}
    <div class="add-form" id="add-form">
        <form action="/restaurant/manage-restaurant/food-menu/order-set/detail/add-item/{{ $orderSet->id }}" method="POST" enctype="multipart/form-data">
            <div class="flex justify-between">
                <h1 class="font-bold text-xl text-white">FOOD ITEM INVENTORY</h1>
                <button class="bg-submitButton text-white rounded-md w-28 h-10 text-sm" type="submit">Add</button>
            </div>
            @csrf
            <div class="w-full bg-adminViewAccountHeaderColor2 mt-3">
                <div class="grid grid-cols-12 items-center text-center font-bold h-16 px-5">
                    <div class="col-span-2 bg-white py-2">
                        <input type="checkbox" name="select-all-food-item" id="select-all-food-item" class="mr-4"> 
                        <label class="text-manageRestaurantSidebarColorActive">Select All</label>
                    </div>
                    <div class="col-span-1"> </div>
                    <div class="col-span-1">Id</div>
                    <div class="col-span-2">Name</div>
                    <div class="col-span-5">Description</div>
                    <div class="col-span-1">Price</div>
                </div>
                @if (!$foodItems->isEmpty())
                    @php
                        $count = 1
                    @endphp
                    @foreach ($foodItems as $foodItem)  
                        @if ($count % 2 == 0)
                            <div class="bg-manageFoodItemHeaderBgColor grid grid-cols-12 justify-items-center items-center h-10 px-5 mb-2">
                                <div class="col-span-2">
                                    <input type="checkbox" class="foodItem" name="foodItem[]" value="{{ $foodItem->id }}">
                                </div>
                                <div class="col-span-1">
                                    <span class="hidden">{{ $foodItem->foodItemImage }}</span>
                                    <img src="{{ asset('uploads/restaurantAccounts/foodItem/'.$id.'/'.$foodItem->foodItemImage) }}" alt="foodItemImage" class="w-8 h-7">
                                </div>
                                <div class="col-span-1">{{ $foodItem->id }}</div>
                                <div class="col-span-2">{{ $foodItem->foodItemName }}</div>
                                <div class="col-span-5">{{ $foodItem->foodItemDescription }}</div>
                                <div class="col-span-1">{{ $foodItem->foodItemPrice }}</div>
                            </div>
                        @else
                            <div class="bg-white grid grid-cols-12 justify-items-center items-center h-10 px-5 mb-2">
                                <div class="col-span-2">
                                    <input type="checkbox" class="foodItem" name="foodItem[]" value="{{ $foodItem->id }}">
                                </div>
                                <div class="col-span-1">
                                    <span class="hidden">{{ $foodItem->foodItemImage }}</span>
                                    <img src="{{ asset('uploads/restaurantAccounts/foodItem/'.$id.'/'.$foodItem->foodItemImage) }}" alt="foodItemImage" class="w-8 h-7">
                                </div>
                                <div class="col-span-1">{{ $foodItem->id }}</div>
                                <div class="col-span-2">{{ $foodItem->foodItemName }}</div>
                                <div class="col-span-5">{{ $foodItem->foodItemDescription }}</div>
                                <div class="col-span-1">{{ $foodItem->foodItemPrice }}</div>
                            </div>
                        @endif
                        @php
                            $count++
                        @endphp
                    @endforeach
                @endif

                @if ($foodItems->isEmpty())
                    <div class="mx-auto w-full mt-2 grid grid-cols-1 items-center bg-red-400 h-14 shadow-xl rounded-lg">
                        <div class="text-center text-white">There are no Food Item right now</div>
                    </div>
                @endif
            </div>
        </form>
    </div>
    <div class="overlay"></div>
</div>
@endsection