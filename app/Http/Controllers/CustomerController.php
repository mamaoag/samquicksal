<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Promo;
use App\Models\FoodSet;
use App\Models\FoodItem;
use App\Models\OrderSet;
use App\Models\StampCard;
use App\Models\StoreHour;
use App\Models\FoodSetItem;
use Illuminate\Http\Request;
use App\Models\PromoMechanics;
use App\Models\StampCardTasks;
use Illuminate\Support\Carbon;
use App\Models\CustomerAccount;
use App\Models\OrderSetFoodSet;
use App\Models\UnavailableDate;
use App\Models\OrderSetFoodItem;
use App\Models\CustomerStampCard;
use App\Models\RestaurantAccount;
use App\Models\RestaurantTaskList;
use App\Models\RestaurantRewardList;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Mail\RestaurantFormAppreciation;
use App\Models\CustomerQueue;

class CustomerController extends Controller
{
    public $RESTAURANT_IMAGE_PATH = "http://192.168.1.53:8000/uploads/restaurantAccounts/logo";
    public $CUSTOMER_IMAGE_PATH = "http://192.168.1.53:8000/uploads/customerAccounts/logo";
    public $ACCOUNT_NO_IMAGE_PATH = "http://192.168.1.53:8000/images";
    public $POST_IMAGE_PATH = "http://192.168.1.53:8000/uploads/restaurantAccounts/post";
    public $PROMO_IMAGE_PATH = "http://192.168.1.53:8000/uploads/restaurantAccounts/promo";
    public $ORDER_SET_IMAGE_PATH = "http://192.168.1.53:8000/uploads/restaurantAccounts/orderSet";

    
    // public $CUSTOMER_IMAGE_PATH = "https://www.samquicksal.com/uploads/customerAccounts/logo";
    // public $ACCOUNT_NO_IMAGE_PATH = "https://www.samquicksal.com/images";
    // public $POST_IMAGE_PATH = "https://www.samquicksal.com/uploads/restaurantAccounts/post";
    // public $PROMO_IMAGE_PATH = "https://www.samquicksal.com/uploads/restaurantAccounts/promo";
    // public $ORDER_SET_IMAGE_PATH = "https://www.samquicksal.com/uploads/restaurantAccounts/orderSet";

    public function convertDays($day){
        if ($day == "MO"){
            return "Monday";
        } else if ($day == "TU"){
            return "Tuesday";
        } else if ($day == "WE"){
            return "Wednesday";
        } else if ($day == "TH"){
            return "Thursday";
        } else if ($day == "FR"){
            return "Friday";
        } else if ($day == "SA"){
            return "Saturday";
        } else if ($day == "SU"){
            return "Sunday";
        } else {
            return "";
        }
    }
    public function convertMonths($month){
        switch ($month) {
            case '1':
                return "January";
                break;
            case '2':
                return "February";
                break;
            case '3':
                return "March";
                break;
            case '4':
                return "April";
                break;
            case '5':
                return "May";
                break;
            case '6':
                return "June";
                break;
            case '7':
                return "July";
                break;
            case '8':
                return "August";
                break;
            case '9':
                return "September";
                break;
            case '10':
                return "October";
                break;
            case '11':
                return "November";
                break;
            case '12':
                return "December";
                break;
            default:
                return "";
                break;
        }
    }

    public function customers(){
        $account = CustomerAccount::all();
        return response()->json([$account]);
    }
    public function restaurants(){
        $account = RestaurantAccount::all();
        return response()->json([$account]);
    }




    // RESTAURANT ROUTES
    // -------LIST OF RESTAURANTS------------ //
    public function getListOfRestaurants(){
        $finalData = array();
        $restaurants = RestaurantAccount::select('id', 'rName', 'rAddress', 'rLogo')->get();
        foreach ($restaurants as $restaurant){
            $getAvailability = "";
            $rSchedule = "";

            $restaurantUnavailableDates = UnavailableDate::select('unavailableDatesDate')->where('restAcc_id', $restaurant->id)->get();
            foreach ($restaurantUnavailableDates as $restaurantUnavailableDate) {
                if($restaurantUnavailableDate->unavailableDatesDate == date("Y-m-d")){
                    $getAvailability = "Closed";
                } else {
                    $getAvailability = "Open";
                }
            }

            if($getAvailability == "Closed"){
                $rSchedule = "Closed Now";
            } else {
                $restaurantStoreHours = StoreHour::where('restAcc_id', $restaurant->id)->get();
                foreach ($restaurantStoreHours as $restaurantStoreHour){
                    foreach (explode(",", $restaurantStoreHour->days) as $day){
                        if($this->convertDays($day) == date('l')){
                            $currentTime = date("H:i");
                            if($currentTime < $restaurantStoreHour->openingTime || $currentTime > $restaurantStoreHour->closingTime){
                                $rSchedule = "Closed Now";
                            } else {
                                $openingTime = date("g:i a", strtotime($restaurantStoreHour->openingTime));
                                $closingTime = date("g:i a", strtotime($restaurantStoreHour->closingTime));
                                $rSchedule = "Open today at ".$openingTime." to ".$closingTime;
                            }
                        }
                    }
                }
            }
            if($rSchedule == ""){
                $rSchedule = "Closed Now";
            }

            $finalImageUrl = "";
            if ($restaurant->rLogo == ""){$finalImageUrl = $this->ACCOUNT_NO_IMAGE_PATH.'/resto-default.png';
                
            } else {
                $finalImageUrl = $this->RESTAURANT_IMAGE_PATH.'/'.$restaurant->id.'/'. $restaurant->rLogo;
            }

            array_push($finalData, [
                'id' => $restaurant->id,
                'rName' => $restaurant->rName,
                'rAddress' => $restaurant->rAddress,
                'rLogo' => $finalImageUrl,
                'rSchedule' => $rSchedule
            ]);
        }
        return response()->json($finalData);
    }
    // -------RESTAURANT ABOUT BY ID------------ //
    public function getRestaurantAboutInfo($id){
        $account = RestaurantAccount::select('rName', 'rAddress', 'rLogo', 'rNumberOfTables')->where('id', $id)->first();
        $posts = Post::where('restAcc_id', $id)->get();

        $storePosts = array();
        foreach($posts as $post){
            array_push($storePosts, [
                'image' => $this->POST_IMAGE_PATH."/".$id."/".$post->postImage,
                'description' => $post->postDesc,
            ]);
        }

        $finalSchedule = array();
        $storeHours = StoreHour::where('restAcc_id', $id)->get();
        foreach($storeHours as $storeHour){
            $openingTime = date("g:i a", strtotime($storeHour->openingTime));
            $closingTime = date("g:i a", strtotime($storeHour->closingTime));
            foreach (explode(",", $storeHour->days) as $day){
                array_push($finalSchedule, [
                    'Day' => $this->convertDays($day),
                    'Opening' => $openingTime,
                    'Closing' => $closingTime,
                ]);
            }
        }


        $finalImageUrl = "";
        if ($account->rLogo == ""){
            $finalImageUrl = $this->ACCOUNT_NO_IMAGE_PATH.'/resto-default.png';
        } else {
            $finalImageUrl = $this->RESTAURANT_IMAGE_PATH.'/'.$id.'/'. $account->rLogo;
        }

        return response()->json([
            'rName' => $account->rName,
            'rAddress' => $account->rAddress,
            'rImage' => $finalImageUrl,
            'rSchedule' => $finalSchedule,
            'rTableCapacity' => $account->rNumberOfTables,
            'rTableStatus' => 5,
            'rReservedTables' => 3,
            'rNumberOfPeople' => 16,
            'rNumberOfQueues' => 6,
            'rPosts' => $storePosts,
        ]);
    }
    // -------RESTAURANT ABOUT BY ID------------ //
    public function getRestaurantsPromoDetailInfo($promoId, $restaurantId){
        $finalMechanics = array();
        $promo = Promo::where('id', $promoId)->first();
        $mechanics = PromoMechanics::where('promo_id', $promoId)->get();
        
        foreach($mechanics as $mechanic){
            array_push($finalMechanics, [
                'mechanic' => $mechanic->promoMechanic,
            ]);
        }

        $orderdate1 = explode('-', $promo->promoStartDate);
        $month1 = $this->convertMonths($orderdate1[1]);
        $year1 = $orderdate1[0];
        $day1  = $orderdate1[2];

        $orderdate2 = explode('-', $promo->promoEndDate);
        $month2 = $this->convertMonths($orderdate2[1]);
        $year2 = $orderdate2[0];
        $day2  = $orderdate2[2];
        
        return response()->json([
            'promoTitle' => $promo->promoTitle,
            'promoDescription' => $promo->promoDescription,
            'promoStartDate' => $month1." ".$day1.", ".$year1,
            'promoEndDate' => $month2." ".$day2.", ".$year2,
            'promoImage' => $this->PROMO_IMAGE_PATH."/".$restaurantId."/".$promo->promoImage,
            'promoMechanics' => $finalMechanics,
        ]);

    }
    public function getRestaurantsRewardsInfo($id){
        $finalStampTasks = array();
        $stamp = StampCard::where('restAcc_id', $id)->first();
        if($stamp != null){
            $stampReward = RestaurantRewardList::where('restAcc_id', $id)->where('id', $stamp->stampReward_id)->first();
            $stampTasks = StampCardTasks::where('stampCards_id', $stamp->id)->get();
            foreach($stampTasks as $stampTask){
                $task = RestaurantTaskList::where('restAcc_id', $id)->where('id', $stampTask->restaurantTaskLists_id)->first();
                if($task->taskCode == "SPND"){
                    array_push($finalStampTasks, [
                        'task' => "Spend ".$task->taskInput." pesos in 1 visit only"
                    ]);
                } else if($task->taskCode == "BRNG"){
                    array_push($finalStampTasks, [
                        'task' => "Bring ".$task->taskInput." friends in our store"
                    ]);
                } else if($task->taskCode == "ORDR"){
                    array_push($finalStampTasks, [
                        'task' => "Order ".$task->taskInput." add on/s per visit"
                    ]);
                } else {
                    array_push($finalStampTasks, [
                        'task' => $task->taskDescription
                    ]);
                }
            }
    
            $finalStampReward = "";
            if($stampReward->rewardCode == "DSCN"){
                $finalStampReward = "Discount ".$stampReward->rewardInput."% in a Total Bill";
            } else if($stampReward->rewardCode == "FRPE"){
                $finalStampReward = "Free ".$stampReward->rewardInput." person in a group";
            } else {
                $finalStampReward = $stampReward->rewardDescription;
            }
    
            $orderdate = explode('-', $stamp->stampValidity);
            $month = $this->convertMonths($orderdate[1]);
            $year = $orderdate[0];
            $day  = $orderdate[2];
            $finalValidity = "Valid Until: ".$month." ".$day.", ".$year;
            $finalStampCapacity = $stamp->stampCapacity;
        } else {
            $finalStampReward = null;
            $finalValidity = null;
            $finalStampTasks = null;
            $finalStampCapacity = null;
        }
        

        $finalPromos = array();
        $promos = Promo::select('id', 'promoTitle', 'promoImage')->where('restAcc_id', $id)->where('promoPosted', "Posted")->get();
        foreach ($promos as $promo){
            array_push($finalPromos,[
                'promoId' => $promo->id,
                'promoName' => $promo->promoTitle,
                'promoImage' => $this->PROMO_IMAGE_PATH."/".$id."/".$promo->promoImage,
            ]);
        }
        
        if($promos->isEmpty()){
            $finalPromos = null;
        }

        return response()->json([
            'stampReward' => $finalStampReward,
            'stampValidity' => $finalValidity,
            'stampCapacity' => $finalStampCapacity,
            'stampTasks' => $finalStampTasks,
            'promos' => $finalPromos,
        ]);
    }
    public function getRestaurantsMenuInfo($id){
        $orderSets = OrderSet::where('restAcc_id', $id)->get();

        $finalData = array();

        foreach ($orderSets as $orderSet){
            $foodSets = array();
            $orderSetFoodSets = OrderSetFoodSet::where('orderSet_id', $orderSet->id)->get();
            $orderSetFoodItems = OrderSetFoodItem::where('orderSet_id', $orderSet->id)->get();

            // GET FOOD SETS
            if(!$orderSetFoodSets->isEmpty()){
                foreach($orderSetFoodSets as $orderSetFoodSet){
                    $foodItems = array();
                    $foodSetName = FoodSet::select('foodSetName', 'id')->where('id', $orderSetFoodSet->foodSet_id)->where('restAcc_id', $id)->first();
                    $foodSetItems = FoodSetItem::where('foodSet_id', $foodSetName->id)->get();
                    foreach($foodSetItems as $foodSetItem){
                        $foodItemName = FoodItem::where('id', $foodSetItem->foodItem_id)->where('restAcc_id', $id)->first();
                        array_push($foodItems, $foodItemName->foodItemName);
                    }
                    array_push($foodSets, [
                        'foodSetName' => $foodSetName->foodSetName,
                        'foodItem' => $foodItems,
                    ]);
                }
            }
            
            // GET OTHER FOOD SET (FOOD ITEMS)
            if(!$orderSetFoodItems->isEmpty()){
                $foodItems = array();
                foreach($orderSetFoodItems as $orderSetFoodItem){
                    $foodItemName = FoodItem::where('id', $orderSetFoodItem->foodItem_id)->where('restAcc_id', $id)->first();
                    array_push($foodItems, $foodItemName->foodItemName);
                }
                array_push($foodSets, [
                    'foodSetName' => "Others",
                    'foodItem' => $foodItems,
                ]);
            }

            array_push($finalData, [
                'orderSetName' => $orderSet->orderSetName,
                'orderSetTagline' => $orderSet->orderSetTagline,
                'orderSetDescription' => $orderSet->orderSetDescription,
                'orderSetPrice' => "Price: ".$orderSet->orderSetPrice,
                'orderSetImage' => $this->ORDER_SET_IMAGE_PATH."/".$id."/".$orderSet->orderSetImage,
                'foodSet' => $foodSets,
            ]);
        }

        return response()->json($finalData);
    }
    public function getListOfPromos(){
        $finalData = array();

        $promos = Promo::orderBy('id', 'DESC')->where('promoPosted', "Posted")->get();

        foreach ($promos as $promo){
            $restaurantInfo = RestaurantAccount::select('id', 'rLogo', 'rAddress')->where('id', $promo->restAcc_id)->first();
            $finalImageUrl = "";
            if ($restaurantInfo->rLogo == ""){
                $finalImageUrl = $this->ACCOUNT_NO_IMAGE_PATH.'/resto-default.png';
            } else {
                $finalImageUrl = $this->RESTAURANT_IMAGE_PATH.'/'.$restaurantInfo->id.'/'. $restaurantInfo->rLogo;
            }
            

            $orderdate1 = explode('-', $promo->promoStartDate);
            $month1 = $this->convertMonths($orderdate1[1]);
            $year1 = $orderdate1[0];
            $day1  = $orderdate1[2];

            $orderdate2 = explode('-', $promo->promoEndDate);
            $month2 = $this->convertMonths($orderdate2[1]);
            $year2 = $orderdate2[0];
            $day2  = $orderdate2[2];
        
            array_push($finalData, [
                'restaurantImage' => $finalImageUrl,
                'restaurantAddress' => $restaurantInfo->rAddress,
                'restaurantId' => $restaurantInfo->id,
                'promoId' => $promo->id,
                'promoTitle' => $promo->promoTitle,
                'promoStartDate' => $month1." ".$day1.", ".$year1,
                'promoEndDate' => $month2." ".$day2.", ".$year2,
            ]);
        }
        return response()->json($finalData);
    }
    public function getRestaurantChooseOrderSet($id, $custId){
        $finalData = array();
        $finalRewardStatus = "";
        $finalRewardType = "";
        $finalRewardInput = "";

        $restaurant = RestaurantAccount::select('rName', 'rTimeLimit', 'rCapacityPerTable')->where('id', $id)->first();
        $orderSets = OrderSet::where('restAcc_id', $id)->get();
        $getStamp = StampCard::select('stampValidity', 'stampReward_id')->where('restAcc_id', $id)->first();

        if($getStamp == null){
            $finalRewardStatus = "Incomplete";
        } else {
            $getReward = RestaurantRewardList::where('restAcc_id', $id)->where('id', $getStamp->stampReward_id)->first();

            $checkIfComplete = CustomerStampCard::where('customer_id', $custId)
                            ->where('restAcc_id', $id)
                            ->where('stampValidity', $getStamp->stampValidity)
                            ->where('claimed', "No")
                            ->where('status', "Complete")->first();
            if($checkIfComplete == null){
                $finalRewardStatus = "Incomplete";
                $finalRewardType = "";
                $finalRewardInput = 0;
            } else {
                $finalRewardType = $getReward->rewardCode;
                $finalRewardInput = $getReward->rewardInput;
                $finalRewardStatus = "Complete";
            }
        }
        
        foreach ($orderSets as $orderSet){
            array_push($finalData, [
                'orderSetId' => $orderSet->id,
                'orderSetPrice' => $orderSet->orderSetPrice,
                'orderSetName' => $orderSet->orderSetName,
                'orderSetTagline' => $orderSet->orderSetTagline,
                'orderSetImage' => $this->ORDER_SET_IMAGE_PATH."/".$id."/".$orderSet->orderSetImage,
            ]);
        }
        return response()->json([
            'restaurantName' => $restaurant->rName,
            'rTimeLimit' => $restaurant->rTimeLimit,
            'rCapacityPerTable' => $restaurant->rCapacityPerTable,
            'rewardStatus' => $finalRewardStatus,
            'rewardType' => $finalRewardType,
            'rewardInput' => $finalRewardInput,
            'orderSets' => $finalData,
        ]);
    }
    public function getReservationDateAndTimeForm($id){
        $unavailableDates = UnavailableDate::select('unavailableDatesDate')->where('restAcc_id', $id)->get();
        $currentDate = date('Y-m-d');
        $storeDates = array();

        for($i=0; $i<7; $i++){
            $currentDateTime2 = date('Y-m-d', strtotime($currentDate. ' + 1 days'));
            array_push($storeDates, $currentDateTime2);
            $currentDate = $currentDateTime2;
        }

        if(!$unavailableDates->isEmpty()){
            foreach ($unavailableDates as $unavailableDate){
                if (($key = array_search($unavailableDate->unavailableDatesDate, $storeDates)) !== false) {
                    unset($storeDates[$key]);
                }
            }
        }

        $newStoreDates = array_values($storeDates);
        $storeDays = array();
        foreach ($newStoreDates as $newStoreDate){
            array_push($storeDays, date('l', strtotime($newStoreDate)));
        }

        $finalSchedule = array();
        $storeHours = StoreHour::where('restAcc_id', $id)->get();
        foreach($storeHours as $storeHour){
            $openingTime = date("g:i a", strtotime($storeHour->openingTime));
            $closingTime = date("g:i a", strtotime($storeHour->closingTime));
            foreach (explode(",", $storeHour->days) as $day){
                array_push($finalSchedule, [
                    'Day' => $this->convertDays($day),
                    'Opening' => $openingTime,
                    'Closing' => $closingTime,
                ]);
            }
        }


        return response()->json([
            'storeDates' => $storeDates,
            'newStoreDates' => $newStoreDates,
            'storeDays' => $storeDays,
        ]);
    }
    

















    // -------LOGIN CUSTOMER------------ //
    public function loginCustomer(Request $request){
        $request->validate([
            'emailAddress' => 'required',
            'password' => 'required',
        ]);
        $account = CustomerAccount::where('emailAddress', $request->emailAddress)->first();
        if(empty($account)){
            return response()->json([
                'id' => null,
                'status' => "Account does not exist",
            ]);
        } else {
            if(Hash::check($request->password, $account->password)){
                return response()->json([
                    'id' => $account->id,
                    'status' => "Login Successfully",
                ]);
            } else {
                return response()->json([
                    'id' => null,
                    'status' => "Account does not exist",
                ]);
            }
        }
    }
    // -------REGISTER CUSTOMER------------ //
    public function registerCustomer(Request $request){
        $request->validate([
            'name' => 'required',
            'emailAddress' => 'required',
            'contactNumber' => 'required',
            'password' => 'required',
        ]);

        $existingAccount = CustomerAccount::where('emailAddress', $request->emailAddress)->first();
        
        if($existingAccount != null){
            return response()->json([
                'id' => null,
                'status' => "Email Address is already existing",
            ]);
        } else {
            
            // $details = [
            //     'applicantName' => $request->name,
            // ];

            // Mail::to($request->emailAddress)->send(new RestaurantFormAppreciation($details));

            $customer = CustomerAccount::create([
                'name' => $request->name,
                'emailAddress' => $request->emailAddress,
                'emailAddressVerified' => "No",
                'contactNumber' => $request->contactNumber,
                'contactNumberVerified' => "No",
                'password' => Hash::make($request->password),
                'profileImage' => "",
            ]);
            
            if(!is_dir('uploads')){
                mkdir('uploads');
                mkdir('uploads/customerAccounts');
                mkdir('uploads/customerAccounts/logo');
                mkdir('uploads/restaurantAccounts');
                mkdir('uploads/restaurantAccounts/foodItem');
                mkdir('uploads/restaurantAccounts/foodSet');
                mkdir('uploads/restaurantAccounts/orderSet');
                mkdir('uploads/restaurantAccounts/post');
                mkdir('uploads/restaurantAccounts/gcashQr');
                mkdir('uploads/restaurantAccounts/logo');
                mkdir('uploads/restaurantAccounts/promo');
            }

            mkdir('uploads/customerAccounts/logo/'.$customer->id);

            return response()->json([
                'id' => $customer->id,
                'status' => "Registered Successfully",
            ]);
        }
    }
    // -------GET CUSTOMER HOMEPAGE INFO------------ //
    public function getCustomerHomepageInfo($id){
        $account = CustomerAccount::select('name', 'emailAddress', 'profileImage')->where('id', $id)->first();

        $finalImageUrl = "";
        if($account->profileImage == ""){
            $finalImageUrl = $this->ACCOUNT_NO_IMAGE_PATH.'/user-default.png';
        } else {
            $finalImageUrl = $this->CUSTOMER_IMAGE_PATH.'/'.$id.'/'. $account->profileImage;
        }

        return response()->json([
            'name' => $account->name,
            'emailAddress' => $account->emailAddress,
            'profileImage' => $finalImageUrl,
            'status' => "onGoing",
        ]);
    }
    public function getCustomerAccountInfo($id){
        $account = CustomerAccount::where('id', $id)->first();

        $finalImageUrl = "";
        if($account->profileImage == ""){
            $finalImageUrl = $this->ACCOUNT_NO_IMAGE_PATH.'/user-default.png';
        } else {
            $finalImageUrl = $this->CUSTOMER_IMAGE_PATH.'/'.$id.'/'. $account->profileImage;
        }

        return response()->json([
            'name' => $account->name,
            'emailAddress' => $account->emailAddress,
            'emailAddressVerified' => $account->emailAddressVerified,
            'contactNumber' => $account->contactNumber,
            'contactNumberVerified' => $account->contactNumberVerified,
            'password' => $account->password,
            'profileImage' => $finalImageUrl,
        ]);
    }
    public function submitQueueForm(Request $request){
        CustomerQueue::create([
            'customer_id' => $request->customer_id,
            'restAcc_id' => $request->restAcc_id,
            'orderSet_id' => $request->orderSet_id,
            'status' => "Pending",
            'cancellable' => "Yes",
            'numberOfPersons' => $request->numberOfPersons,
            'numberOfTables' => $request->numberOfTables,
            'hoursOfStay' => $request->hoursOfStay,
            'numberOfChildren' => $request->numberOfChildren,
            'numberOfPwd' => $request->numberOfPwd,
            'totalPwdChild' => $request->totalPwdChild,
            'notes' => $request->notes,
            'rewardStatus' => $request->rewardStatus,
            'rewardType' => $request->rewardType,
            'rewardInput' => $request->rewardInput,
            'totalPrice' => $request->totalPrice,
        ]);

        return response()->json([
            'status' => "Success"
        ]);
    }
}
