import 'dart:convert';
import 'package:ahshiaka/bloc/layout_cubit/categories_cubit/categories_cubit.dart';
import 'package:ahshiaka/bloc/profile_cubit/profile_cubit.dart';
import 'package:ahshiaka/models/checkout/PaymentGetwayesModel.dart';
import 'package:ahshiaka/models/checkout/addresses_model.dart';
import 'package:ahshiaka/models/checkout/coupons_model.dart';
import 'package:ahshiaka/models/checkout/orders_model.dart';
import 'package:ahshiaka/models/checkout/shipping_methods_model.dart';
import 'package:ahshiaka/models/checkout/shipping_model.dart';
import 'package:ahshiaka/shared/components.dart';
import 'package:ahshiaka/view/layout/bottom_nav_screen/tabs/profile/my_orders/my_orders_screen.dart';
import 'package:easy_localization/easy_localization.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:tabby_flutter_sdk/tabby_flutter_sdk.dart' as tabby;
import '../../../models/categories/products_model.dart';
import '../../../repository/checkout_repository.dart';
import '../../../shared/cash_helper.dart';
import '../../../utilities/app_util.dart';
import 'checkout_state.dart';

class CheckoutCubit extends Cubit<CheckoutState> {
  CheckoutCubit() : super(CheckoutInitial());

  static CheckoutCubit get(context) => BlocProvider.of(context);

  List<ProductModel> cartList = [];
  List<int> qty = [];
  double total = 0.0;
  int couponValue = 0;
  bool flatRateApply = false;
  bool couponApplied = false;
  var couponController = TextEditingController();

  fetchCartList(context) async {
    String email = await CashHelper.getSavedString("email", "");
    qty.clear();
    total = 0.0;
    cartList.clear();
    String cartListString = await CashHelper.getSavedString("${email}cartList", "");
    if (cartListString == "") {
      return;
    } else {
      print(cartListString);
      jsonDecode(cartListString).forEach((element) {
        cartList.add(ProductModel.fromJson(element));
      });
      List<ProductModel> favProducts = CategoriesCubit.get(context).favProducts;

      for (var element in cartList) {
        for(var favProduct in favProducts){
          if(element.id == favProduct.id){
            element.fav = true;
          }
        }
        total += double.parse(element.price!.toString()) *
            int.parse(element.qty.toString());
        qty.add(element.qty!);
      }
    }
    total -= couponValue;

    emit(CheckoutChangeState());
  }

  removeCartItemItem(ProductModel product, int index,context) async {
    String email = await CashHelper.getSavedString("email", "");
    qty.removeAt(index);
    cartList.remove(product);
    CashHelper.setSavedString("${email}cartList", jsonEncode(cartList));
    fetchCartList(context);
    emit(CheckoutChangeState());
  }


  changeQuantity(id, qty, type,context) async {
    // print(qty);
    for (var element in cartList) {
      if (element.id.toString() == id.toString()) {
        element.qty = qty;
        if (type == "increment") {
          total += double.parse(element.price!);
        } else {
          total -= double.parse(element.price!);
        }
      }
    }
    // print(cartList[0].qty);
    String email = await CashHelper.getSavedString("email", "");
    CashHelper.setSavedString("${email}cartList", jsonEncode(cartList));
    fetchCartList(context);
    emit(CheckoutChangeState());
  }

  List<CouponsModel> couponsModel = [];

  fetchCoupons() async {
    try {
      var response = await CheckoutRepository.fetchCoupons();
      response.forEach((element) {
        couponsModel.add(CouponsModel.fromJson(element));
      });
    } catch (e) {
      return Future.error(e);
    }
  }

  applyCoupon(context) async {
    if(AppUtil.isEmailValidate(couponController.text)){
      AppUtil.dialog2(context, "", [
        const LoadingWidget(),
        const SizedBox(height: 30,),
      ]);
      var map = await CheckoutRepository.hasCode(couponController.text);
      Navigator.of(context,rootNavigator: true).pop();
      if(map is !List){
        for (var element in couponsModel) {
          if (map['code'].toString().toLowerCase() == element.code) {
            for (var data in element.metaData!) {
              if(data.key == "_no_free_shipping_checkbox"){
                if(data.value == "yes"){
                  flatRateApply = true;
                  couponApplied = true;
                  break;
                }
              }
              if(!flatRateApply){
                couponValue = int.parse(element.amount!.split(".")[0]);
                total = total - couponValue;
                if (total < 0) {
                  total = 0;
                }
                couponApplied = true;
                break;
              }
            }
          }
        }
      }else{
        AppUtil.errorToast(context, "couponNotFound".tr());
        return;
      }
    }else {
      for (var element in couponsModel) {
        if (couponController.text == element.code) {
          for (var data in element.metaData!) {
            if (data.key == "_no_free_shipping_checkbox") {
              if (data.value == "yes") {
                flatRateApply = true;
                couponApplied = true;
                break;
              }
            }
            if (!flatRateApply) {
              couponValue = int.parse(element.amount!.split(".")[0]);
              total = total - couponValue;
              if (total < 0) {
                total = 0;
              }
              couponApplied = true;
              break;
            }
          }
        }
      }
    }
    if (couponApplied) {
      AppUtil.successToast(context, "couponAppliedSuccessfully".tr());
    } else {
      AppUtil.errorToast(context, "couponNotFound".tr());
    }
    emit(ApplyCoupon());
  }

  cancelCoupon() {
    total += couponValue;
    couponValue = 0;
    flatRateApply = false;
    couponApplied = false;
    emit(ApplyCoupon());
  }

  // addresses variables
  var nameController = TextEditingController();
  var surNameController = TextEditingController();
  var phoneController = TextEditingController();
  var emailController = TextEditingController();
  var addressController = TextEditingController();
  var stateController = TextEditingController();
  var address2Controller = TextEditingController();
  var postCodeController = TextEditingController();
  var cityController = TextEditingController();
  var countryController = TextEditingController();
  bool defaultAddress = false;
  var newAddressFormKey = GlobalKey<FormState>();
  changeDefaultState() {
    defaultAddress = !defaultAddress;
    emit(AddressesState());
  }

  saveAddress(context, {String? address_id}) async {
    Map<String, dynamic> formData = {
      "shipping_first_name": nameController.text,
      "shipping_last_name": surNameController.text,
      "shipping_country": countryController.text,
      "shipping_address_1": addressController.text,
      "shipping_city": cityController.text,
      "shipping_company": "test any value",
      "shipping_address_2": address2Controller.text,
      "shipping_state": stateController.text,
      "shipping_postcode": postCodeController.text,
      "shipping_phone": phoneController.text,
      "shipping_email": emailController.text
    };
    String email = await CashHelper.getSavedString("email", "");

    try {
      var response = await CheckoutRepository.saveAddress(
          formData,email,address_id: address_id);
      if(response is String){
        saveAddress(context);
        return;
      }
      Navigator.of(context,rootNavigator: true).pop();
      Navigator.of(context,rootNavigator: true).pop();
      AppUtil.successToast(context, "addedSuccessfully".tr());
      if(address_id==null) {
        nameController.clear();
        surNameController.clear();
        countryController.clear();
        addressController.clear();
        cityController.clear();
        address2Controller.clear();
        stateController.clear();
        postCodeController.clear();
        phoneController.clear();
        emailController.clear();
      }
    } catch (e) {
      return Future.error(e);
    }
    fetchAddresses();
  }

  ShippingModel? addresses;

  AddressesModel? selectedAddress;

  fetchAddresses() async {
    String email = await CashHelper.getSavedString("email", "");

    try {
      Map<String, dynamic> response = await CheckoutRepository.fetchAddresses(email);
      if(response["shipping"] is !List){
      addresses = ShippingModel.fromJson(response);
      if (addresses!.shipping!.address0!.isNotEmpty) {
        selectedAddress = AddressesModel(
            fullName: addresses!.shipping!.address0![0].shippingFirstName
            ,
            surName: addresses!.shipping!.address0![0].shippingLastName
            ,
            phoneNumber: addresses!.shipping!.address0![0].shippingPhone
            ,
            email: addresses!.shipping!.address0![0].shippingEmail
            ,
            address: addresses!.shipping!.address0![0].shippingAddress1
            ,
            state: addresses!.shipping!.address0![0].shippingState
            ,
            address2: addresses!.shipping!.address0![0].shippingAddress2
            ,
            city: addresses!.shipping!.address0![0].shippingCity,
            postCode: addresses!.shipping!.address0![0].shippingPostcode,
            country: addresses!.shipping!.address0![0].shippingCountry,
            defaultAddress: 0 == 0 ? true : false);
      }
      }else{
        selectedAddress = null;
        addresses = null;
      }

    } catch (e) {
      return Future.error(e);
    }
    emit(AddressesState());
  }

  deleteAddress(addressKey) async {
    String email = await CashHelper.getSavedString("email", "");

    try {
      Map<String, dynamic> response = await CheckoutRepository.deleteAddress(
          addressKey,email);
      fetchAddresses();
    } catch (e) {
      return Future.error(e);
    }
  }

  //shipping
  List<ShippingMethodsModel> shippingMethods = [];
  ShippingMethodsModel? selectedShippingMethods;

  fetchShippingMethods() async {
    shippingMethods.clear();
    try {
      var response = await CheckoutRepository.fetchShippingMethods();
      response.forEach((element) {
        if (element['enabled']) {
          shippingMethods.add(ShippingMethodsModel.fromJson(element));
        }
      });
    } catch (e) {
      return Future.error(e);
    }
  }

//payment
  List<PaymentGetwayesModel> paymentGetaway = [];
  PaymentGetwayesModel? selectedPaymentGetaways;

  fetchPaymentGetaways() async {
    paymentGetaway.clear();
    try {
      var response = await CheckoutRepository.fetchPaymentGetaways();
      response.forEach((element) {
        if (element['enabled']) {
          paymentGetaway.add(PaymentGetwayesModel.fromJson(element));
        }
      });
    } catch (e) {
      return Future.error(e);
    }
  }


//create order
  createOrder(context) async {
    emit(CheckoutLoadingState());
    String email = await CashHelper.getSavedString("email", "");
    if(email!=""){
      selectedAddress!.email = email;
    }else{
      CashHelper.setSavedString("guestEmail", selectedAddress!.email!);
    }
    List lineItems = [];
    List shippingLines = [];
    for (var element in cartList) {
      print("element.mainProductId ${element.mainProductId}");
      lineItems.add({
        "product_id": element.mainProductId ?? "41611",
        "variation_id": element.id.toString(),
        "quantity": element.qty.toString()
      });
    }

    if (selectedShippingMethods != null) {
      shippingLines.add({
        "method_id": selectedShippingMethods!.id,
        "method_title": selectedShippingMethods!.title,
        "total": selectedShippingMethods!.settings!.cost == null
            ? "0"
            : selectedShippingMethods!.settings!.cost!.value
      });
    }
    Map<String,dynamic> formData = {
      "payment_method": selectedPaymentGetaways!.id,
      "payment_method_title": selectedPaymentGetaways!.title,
      "set_paid": false,
      "billing": {
        "first_name": selectedAddress!.fullName,
        "last_name": selectedAddress!.fullName,
        "address_1": selectedAddress!.address,
        "address_2": selectedAddress!.address2,
        "city": selectedAddress!.city,
        "state": selectedAddress!.state,
        "postcode": selectedAddress!.postCode,
        "country": "SA",
        "email": selectedAddress!.email,
        "phone": selectedAddress!.phoneNumber
      },
      "shipping": {
        "first_name": selectedAddress!.fullName,
        "last_name": selectedAddress!.fullName,
        "address_1": selectedAddress!.address,
        "address_2": selectedAddress!.address2,
        "city": selectedAddress!.city,
        "state": selectedAddress!.state,
        "postcode": selectedAddress!.postCode,
        "country": "SA",
        "email": selectedAddress!.email,
        "phone": selectedAddress!.phoneNumber
      },
      "coupon_lines":  couponApplied?[
        {
          "code":couponController.text
        }
      ]:null,
      "line_items": lineItems,
      "shipping_lines": shippingLines,
      "fee_lines": selectedPaymentGetaways!.id == "cod" ?[
        {"name":"Cash on delivery fees", "total": "5.75", "tax_status": "none"}
      ]:null,
    };


    try {
      var response = await CheckoutRepository.createOrder(jsonEncode(formData),customer: ProfileCubit.get(context).profileModel.isEmpty?"0":ProfileCubit.get(context).profileModel[0].id);
      String email = await CashHelper.getSavedString("email", "");
      CashHelper.setSavedString("${email}cartList", "");
      await fetchCartList(context);
      emit(CheckoutChangeState());
      return response;
    } catch (e) {
      emit(CheckoutErrorState());
      return Future.error(e);
    }
  }

  //payment credit cards
  var cardHolderController = TextEditingController();
  var cardNumberController = TextEditingController();
  var expiryDateController = TextEditingController();
  var cvvController = TextEditingController();

  payWithPayfort(orderId, context) async {
    Map formData = {
      "command": "CAPTURE",
      "access_code": "zx0IPmPy5jp1vAz8Kpg7",
      "merchant_identifier": "CycHZxVj",
      "merchant_reference": "XYZ9239-yu898",
      "amount": total.toString(),
      "currency": "AED",
      "language": "en",
      "fort_id": "149295435400084008",
      "signature": "7cad05f0212ed933c9a5d5dffa31661acf2c827a",
      "order_description": "iPhone 6-S",
      "card_holder_name": cardHolderController.text,
      "expiry_date": expiryDateController.text,
      "card_number": cardNumberController.text.trim(),
      "cvv": cvvController.text
    };
    int response = await CheckoutRepository.payWithPayfort(formData);
    if (response == 200) {
      await CheckoutRepository.updateOrder(orderId);
      AppUtil.mainNavigator(context, MyOrdersScreen());
    }
  }

  testTabby(context) async {
    final tabbySdk = tabby.TabbyFlutterSdk();
    tabbySdk.setApiKey("Your Public Key");
    final payload = tabby.TabbyCheckoutPayload(
        merchantCode: "eyewa",
        lang: tabby.Language.en,
        payment: tabby.Payment(
            amount: (selectedPaymentGetaways!=null&&selectedPaymentGetaways!.id=="cod"?(selectedShippingMethods!= null?selectedShippingMethods!.methodId == "flat_rate" ?total+double.parse(selectedShippingMethods!.settings!.cost!.value!):total:total)+5:(selectedShippingMethods!= null?selectedShippingMethods!.methodId == "flat_rate" ?total+double.parse(selectedShippingMethods!.settings!.cost!.value!):total:total)).toString(),
            description: "Just a dest payment",
            currency: tabby.Currency.AED,
            buyer: tabby.Buyer(
                email: "successful.payment@tabby.ai",
                phone: selectedAddress!.phoneNumber!,
                name: "Test Name"),
            order: tabby.Order(
                referenceId: "#xxxx-xxxxxx-xxxx",
                items: [
                  tabby.OrderItem(
                      description: "Jersey",
                      productUrl: "https://tabby.store/p/SKU123",
                      quantity: 1,
                      referenceId: "SKU123",
                      title: "Pink jersey",
                      unitPrice: "300")
                ],
                shippingAmount: "21",
                taxAmount: "0"),
            shippingAddress: tabby.ShippingAddress(
                address: "Sample Address #2", city: "Dubai")));

    tabbySdk.makePayment(context, payload).then((value) {
      print("tabbySdk result ${value}");

      if (value == tabby.TabbyResult.authorized) {
        tabby.showToast(context, "Payment has been authorized", success: true);
        return;
      }
      tabby.showToast(
        context,
        "Payment is ${value.name}",
      );
    });
  }

  List<OrdersModel> pendingOrders = [];
  List<OrdersModel> otherOrders = [];

  fetchOrders(context) async {
    String email ='';
    email = await CashHelper.getSavedString("email", "");
    if(email==""){
      print("hbhbhb ${email}");
      email = await CashHelper.getSavedString("guestEmail", "");
    }
    print("hbhbhb ${email}");

    pendingOrders.clear();
    otherOrders.clear();
    try {
      var response = await CheckoutRepository.fetchOrders(email,customer: ProfileCubit.get(context).profileModel.isEmpty?"0":ProfileCubit.get(context).profileModel[0].id);
      print('kjbhbhjbjhbhjb $response');
      response.forEach((element) {
        if (element['status'] == "pending") {
          pendingOrders.add(OrdersModel.fromJson(element));
        } else {
          otherOrders.add(OrdersModel.fromJson(element));
        }
      });
      emit(CheckoutChangeState());
    } catch (e) {
      return Future.error(e);
    }
  }


  deleteOrder(id,context) async {

    try {
      var response = await CheckoutRepository.deleteOrder(id);
      await fetchOrders(context);
      emit(CheckoutChangeState());
    } catch (e) {
      return Future.error(e);
    }
  }


}

