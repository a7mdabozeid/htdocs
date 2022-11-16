import 'dart:convert';
import 'package:ahshiaka/bloc/layout_cubit/checkout_cubit/checkout_cubit.dart';
import 'package:ahshiaka/models/categories/banner_model.dart';
import 'package:ahshiaka/models/categories/products_model.dart';
import 'package:ahshiaka/models/categories/reviews_model.dart';
import 'package:ahshiaka/models/categories/size_guide_model.dart';
import 'package:ahshiaka/models/home_menu_model.dart';
import 'package:ahshiaka/repository/categories_repository.dart';
import 'package:easy_localization/easy_localization.dart';
import 'package:flutter/cupertino.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:string_similarity/string_similarity.dart';

import '../../../models/categories/categories_model.dart';
import '../../../shared/cash_helper.dart';
import '../../../utilities/app_util.dart';
import 'categories_states.dart';


class CategoriesCubit extends Cubit<CategoriesState> {


  CategoriesCubit() : super(CategoriesInitial());
  static CategoriesCubit get(context) => BlocProvider.of(context);
  List<ProductModel> favProducts = [];

  changeTabState() {
    emit(CategoriesChangeTabState());
  }
  final homeScrollController = ScrollController();

  int selectedCatId = 0;
  var searchController = TextEditingController();

  List<CategoriesModel> allSubCategoriesModel = [];
  List<CategoriesModel> categoriesModel = [];
  List<CategoriesModel> subCategoriesModel = [];
  List<CategoriesModel> subSubCategoriesModel = [];
  int catInitIndex = 0;
  int initialIndex = 0;
  TabController? tapBarController ;
  fetchCategories() async {
    emit(CategoriesLoadingState());
    try{
      List response = await CategoriesRepository.fetchCategories();
      for (var element in response) {
        if(element['parent'] == 0) {
          categoriesModel.add(CategoriesModel.fromJson(element));
        }else{
          allSubCategoriesModel.add(CategoriesModel.fromJson(element));
        }
      }
      if(categoriesModel.isEmpty){
        emit(CategoriesEmptyState());
      }else {
        categoriesModel.removeWhere((element) => element.name=="Uncategorized");
        categoriesModel.removeWhere((element) => element.name=="غير مصنف");
        emit(CategoriesLoadedState());
        fetchSubCategories(categoriesModel[0].id!);
      }
    }catch(e){
      emit(CategoriesErrorState());
      return Future.error(e);
    }
  }

  fetchSubCategories(catId){
    subCategoriesModel.clear();
      for(var element in allSubCategoriesModel) {
        if(element.parent == catId) {
            subCategoriesModel.add(element);
        }
      }
    emit(SubCategoriesChangeState());
  }


  fetchSubSubCategories(catId){
    subSubCategoriesModel.clear();
      for(var element in allSubCategoriesModel) {
          if(element.parent == catId) {
            subSubCategoriesModel.add(element);
          }
      }
    emit(SubCategoriesChangeState());
  }



  List<ProductModel> _productModel = [];
  List<ProductModel> get productModel => _productModel;

  List favList = [];
  final List _selectedAttributeIndex = [];

  List get selectedAttributeIndex => _selectedAttributeIndex;

  final productScrollController = ScrollController();
  int productPage = 1;
  fetchProductsByCategory({required catId , required page , required perPage,filterParams,minPrice,maxPrice,ratingCount, String? name}) async {
    if(page == 0){
      page = 1;
    }
    if(page == 1) {
      productPage=1;
      _productModel.clear();
    }
    favList = favProducts;
    if(page == 1) {
      emit(ProductsLoadingState());
    }else{
      emit(ProductsLoadingPaginateState());
    }
    print('page: $page');
    try{
      List response = await CategoriesRepository.fetchProductsByCategory(catId: catId, page: page, perPage: perPage,filterParams: filterParams,maxPrice: maxPrice,minPrice: minPrice,ratingCount: ratingCount,name: name);
        for (var element in response) {
          productModel.add(ProductModel.fromJson(element));
        }
      if(response.isEmpty){
        productPage--;
      }
      if(productModel.isEmpty){
        emit(ProductsEmptyState());
      }else{
        for ( ProductModel favItem in favList){
          for(ProductModel product in productModel){
            if(favItem.id == product.id){
              product.fav = true;
              break;
            }
          }
        }
        emit(ProductsLoadedState());
      }
    }catch(e){
      emit(ProductsErrorState());
      return Future.error(e);
    }
  }
  List<ProductModel> newArrivalProduct = [];

 fetchNewArrivalProducts({required catId , required page , required perPage,minPrice,maxPrice,ratingCount, String? name}) async {

    favList = favProducts;
    if(page == 1) {
      newArrivalProduct.clear();
      emit(ProductsLoadingState());
    }else{
      emit(ProductsLoadingPaginateState());
    }
    try{
      List response = await CategoriesRepository.fetchProductsByCategory(catId: catId, page: page, perPage: perPage,maxPrice: maxPrice,minPrice: minPrice,ratingCount: ratingCount,name: name);
        for (var element in response) {
          newArrivalProduct.add(ProductModel.fromJson(element));
        }

      if(newArrivalProduct.isEmpty){
        emit(ProductsEmptyState());
      }else{
        for (ProductModel favItem in favList){
          for(ProductModel product in newArrivalProduct){
            if(favItem.id == product.id){
              product.fav = true;
              break;
            }
          }
        }
        emit(ProductsLoadedState());
      }
    }catch(e){
      emit(ProductsErrorState());
      return Future.error(e);
    }
  }

  List<ProductModel> relatedProducts = [];

 fetchRelatedProducts({required catId , required page , required perPage,minPrice,maxPrice,ratingCount, String? name}) async {
   relatedProducts.clear();
    favList = favProducts;
    if(page == 1) {
      emit(RelatedProductsLoadingState());
    }else{
      emit(RelatedProductsLoadingPaginateState());
    }
    try{
      List response = await CategoriesRepository.fetchProductsByCategory(catId: catId, page: page, perPage: perPage,maxPrice: maxPrice,minPrice: minPrice,ratingCount: ratingCount,name: name);
        for (var element in response) {
          relatedProducts.add(ProductModel.fromJson(element));
        }

      if(relatedProducts.isEmpty){
        emit(RelatedProductsEmptyState());
      }else{
        for (ProductModel favItem in favList){
          for(ProductModel product in relatedProducts){
            if(favItem.id == product.id){
              product.fav = true;
              break;
            }
          }
        }
        emit(RelatedProductsLoadedState());
      }
    }catch(e){
      emit(RelatedProductsErrorState());
      return Future.error(e);
    }
  }


  List<ProductModel> recommendedProduct = [];

 fetchRecommendedProducts({required catId , required page , required perPage,minPrice,maxPrice,ratingCount, String? name}) async {
    favList = favProducts;
    if(page == 1) {
      recommendedProduct.clear();
      emit(ProductsLoadingState());
    }else{
      emit(ProductsLoadingPaginateState());
    }
    String recoCatId = await CashHelper.getSavedString("recoCatId", "0");
    try{
      List response = await CategoriesRepository.fetchProductsByCategory(catId: int.parse(recoCatId), page: page, perPage: perPage,maxPrice: maxPrice,minPrice: minPrice,ratingCount: ratingCount,name: name);
      for (var element in response) {
          recommendedProduct.add(ProductModel.fromJson(element));
        }

      if(recommendedProduct.isEmpty){
        emit(ProductsEmptyState());
      }else{
        for (ProductModel favItem in favList){
          for(ProductModel product in recommendedProduct){
            if(favItem.id == product.id){
              product.fav = true;
              break;
            }
          }
        }
        emit(ProductsLoadedState());
      }
    }catch(e){
      emit(ProductsErrorState());
      return Future.error(e);
    }
  }



  fetchFavProducts() async {
    String email = await CashHelper.getSavedString("email", "");
    favProducts.clear();
    // if(email == ""){
    String favListString = await CashHelper.getSavedString("${email}favList", "");
    if(favListString == "[]" || favListString == ""){
      emit(FavEmptyState());
      favProducts = [];
      return [];
    }else{
      jsonDecode(favListString).forEach((element){
        favProducts.add(ProductModel.fromJson(element));
      });
      emit(FavLoadedState());
      return favProducts;
    }
    // }else{
    //   favProducts = await fetchFavProductsWithApi(email);
    // }
  }

  fetchFavProductsWithApi(email) async {
    emit(FavLoadingState());
    favProducts.clear();
    try {
      List response = await CategoriesRepository.fetchFavProductsWithApi(email: email);
      List<ProductModel> productModel = [];
      for (var element in response) {
        productModel.add(ProductModel.fromJson(element));
        favProducts.add(ProductModel.fromJson(element));
      }
      if(favProducts.isEmpty){
        emit(FavEmptyState());
      }else {
        emit(FavLoadedState());
      }
    }catch(e){
      emit(FavErrorState());
      return Future.error(e);
    }
    return favProducts;
  }

  favProduct(ProductModel product,context) async {
    print(product.fav);
    String email = await CashHelper.getSavedString("email", "");
    // if(email =="") {
    for (var element in productModel) {
      if (element.id == product.id) {
        element.fav = true;
      }
    }

    for (var element in newArrivalProduct) {
      if (element.id == product.id) {
        element.fav = true;
      }
    }
    for (var element in recommendedProduct) {
      if (element.id == product.id) {
        element.fav = false;
      }
    }

    for (var element in relatedProducts) {
      if (element.id == product.id) {
        element.fav = false;
      }
    }

    for (var element in CheckoutCubit.get(context).cartList) {
      if (element.id == product.id) {
        element.fav = false;
      }
    }
    for (var e in homeMenuModel) {
      for (var element in e) {
        if (element.id == product.id) {
          element.fav = false;
        }
      }}

      List favList = await fetchFavProducts();
      if (favList.isNotEmpty) {
        for (int i = 0; i < favList.length; i++) {
          if (favList[i].id == product.id) {
            product.fav = false;
            favList.removeAt(i);
            CashHelper.setSavedString("${email}favList", jsonEncode(favList));
            emit(ChangeFavState());
            return;
          }
        }
        product.fav = true;
        favList.add(product);
        CashHelper.setSavedString("${email}favList", jsonEncode(favList));
      } else {
        product.fav = true;
        CashHelper.setSavedString("${email}favList", jsonEncode([product.toJson()]));
      }
      await fetchFavProducts();
      emit(ChangeFavState());
    // }else{
    //   product.fav = !product.fav!;
    //   emit(ChangeFavState());
    //   favProductWithApi(email: email, wishListId: wishlistId, productId: product.id!, favState: product.fav);
    // }

  }

  favProductWithApi({required email, required wishListId, required productId, required favState}) async {
    try {
      if(favState) {
        await CategoriesRepository.favProductWithApi(
            email: email, wishListId: "0", productId: productId);
      }else{
        await CategoriesRepository.unFavProductWithApi(
            email: email, wishListId: "0", productId: productId);
      }
    }catch(e){
      return Future.error(e);
    }
  }

  removeFromFav(ProductModel product,context) async {
    print('kjnfn ${favProducts}');
    String email = await CashHelper.getSavedString("email", "");
      favProducts.removeWhere((element) => element.id == product.id);
    print('kjnfn ${favProducts}');

    for (var element in productModel) {
        if (element.id == product.id) {
          element.fav = false;
        }
      }

      for (var element in newArrivalProduct) {
        if (element.id == product.id) {
          element.fav = false;
        }
      }

      for (var element in recommendedProduct) {
        if (element.id == product.id) {
          element.fav = false;
        }
      }

      for (var element in relatedProducts) {
        if (element.id == product.id) {
          element.fav = false;
        }
      }

      for (var element in CheckoutCubit.get(context).cartList) {
        if (element.id == product.id) {
          element.fav = false;
        }
      }

    for (var e in homeMenuModel) {
      for (var element in e) {
        if (element.id == product.id) {
          element.fav = false;
        }
      }}


    emit(ChangeFavState());
    emit(ProductsLoadedState());
    // if(email == "") {
      await CashHelper.setSavedString("${email}favList", jsonEncode(favProducts));
    // CheckoutCubit.get(context).cartList.forEach((element) {
    //
    // });
    await CashHelper.setSavedString("${email}cartList", jsonEncode(CheckoutCubit.get(context).cartList));

    // await CheckoutCubit.get(context).fetchCartList(context);
    // }else{
    //   await CategoriesRepository.unFavProductWithApi(
    //       email: email, wishListId: wishlistId, productId: product.id);
    // }


  }

  fetchProductAttributes(ProductModel product){
    selectedAttributeIndex.clear();
    for (int i = 0; i < product.attributes!.length; i++) {
      _selectedAttributeIndex.add({"index": 0,"name": product.attributes![i].options==null?product.attributes![i].option:product.attributes![i].options![0]});
    }
  }
  setSelectedAttributesIndex(int selectedSizeIndex,mainListIndex, optionName, int id) {
    _selectedAttributeIndex.insert(mainListIndex, {"index": selectedSizeIndex,"name": optionName,"attributeId": id});
    _selectedAttributeIndex.removeAt(mainListIndex+1);
    emit(AttributeChangeState());
  }

  // CartModel? cartModel;
  addToCart(BuildContext context, ProductModel product, {Map<String,String>? slugs}) async {
    String email = await CashHelper.getSavedString("email", "");
    String cartListString = await CashHelper.getSavedString("${email}cartList", "");
    String cartKey = await CashHelper.getSavedString("cartKey", "");

    Map<String,dynamic> formData={};
    if(cartListString != ""){
      List<ProductModel> cartList = [];
      jsonDecode(cartListString).forEach((element){
        cartList.add(ProductModel.fromJson(element));
      });
      bool exists = false;
      for (var element in cartList) {
        if(element.id == product.id){
          element.qty = element.qty!+1;
          exists = true;
          if(slugs!=null){
           formData = {
              "product_id": product.id!.toString(),
              "quantity": element.qty.toString(),
              "variation": slugs
            };
          }else{
            formData = {
              "product_id": product.id!.toString(),
              "quantity": element.qty.toString(),
            };
          }
          break;
        }
      }
      if(!exists){
        if(slugs!=null){
          formData = {
            "product_id": product.id!.toString(),
            "quantity": "1",
            "variation": slugs
          };
        }else{
          formData = {
            "product_id": product.id!.toString(),
            "quantity": "1",
          };
        }
        cartList.add(product);
        // var response = await CategoriesRepository.addToCart(cartKey, product.id!.toString(), formData);
      }
      print(formData);
      String email = await CashHelper.getSavedString("email", "");

      CashHelper.setSavedString("${email}cartList", jsonEncode(cartList));
    }else{
      CashHelper.setSavedString("${email}cartList", jsonEncode([product.toJson()]));
    }
    if(product.categories!=null && product.categories.isNotEmpty) {
      CashHelper.setSavedString("recoCatId", product.categories[0]['id'].toString());
    }
    AppUtil.successToast(context, "addedSuccessfully".tr(),type: "cart");
    await CheckoutCubit.get(context).fetchCartList(context);
  }

  getVariationId(List<ProductModel> variations,context,{fromFav = false}) async {
    int variantId = 0;
    Map<String,String> slugs = {};
    for (var variant in variations) {
      for (int i = 0; i < variant.attributes!.length; i++) {
        print(variant.id);
        if(StringSimilarity.compareTwoStrings(selectedAttributeIndex[i]['name'].toString().toLowerCase() , variant.attributes![i].option!.toLowerCase())>0.4 && variant.attributes![i].option!.length>=selectedAttributeIndex[i]['name'].toString().length ){
          variantId = variant.id!;
        }else{
          variantId = 0;
          break;
        }
      }
      if(variantId!=0){
        for (var attr in variant.attributes!) {
          for (var mainAttr in attributes) {
            if(attr.id == mainAttr.id){
              slugs.addAll({"attribute_${mainAttr.slug}": attr.option!});
            }
          }
        }
        break;
      }
    }
    if(!fromFav) {
      if (variations.isNotEmpty && variantId == 0) {
        AppUtil.errorToast(context, "noVariationFound".tr());
        return null;
      }
    }
    print(slugs);
    return [variantId,slugs];

  }

   fetchItemInCart(id) async {
     String email = await CashHelper.getSavedString("email", "");
     List<ProductModel> cartList = [];
     String cartListString = await CashHelper.getSavedString("${email}cartList", "");
     if(cartListString == ""){
       return false;
     }else{
       jsonDecode(cartListString).forEach((element){
         cartList.add(ProductModel.fromJson(element));
       });
     }
     for (var element in cartList) {
       if(element.id == id){
         return true;
       }
     }
     return false;
   }

  List<ProductModel> variations = [];
  fetchProductVariations(id)async{
    variations.clear();
    try{
      List response = await CategoriesRepository.fetchProductVariations(id);
      for (var element in response) {
        variations.add(ProductModel.fromJson(element));
      }
    }catch(e){
      return Future.error(e);
    }
  }

  List<ReviewsModel> reviewsModel = [];
  double rating1 = 0;
  double rating2 = 0;
  double rating3 = 0;
  double rating4 = 0;
  double rating5 = 0;
  double averageCount = 0;
  fetchProductsReview(id) async {
    reviewsModel.clear();
     emit(ReviewsLoadingState());
     rating1 = 0;
     rating2 = 0;
     rating3 = 0;
     rating4 = 0;
     rating5 = 0;
     averageCount = 0;
     try{
       List response = await CategoriesRepository.fetchProductsReview(id);
       if(response.isEmpty){
         emit(ReviewsEmptyState());
       }else {
         double sumReviews = 0;
         for (var element in response) {
           if(element['rating'] == 1){
             rating1++;
           }else if(element['rating'] == 2){
             rating2++;
           }else if(element['rating'] == 3){
             rating3++;
           }else if(element['rating'] == 4){
             rating4++;
           }else if(element['rating'] == 5){
             rating5++;
           }
          sumReviews += element['rating']!;
           reviewsModel.add(ReviewsModel.fromJson(element));
         }
         averageCount = sumReviews/reviewsModel.length;

         emit(ReviewsLoadedState());
       }
     }catch(e){
       emit(ReviewsErrorState());
       return Future.error(e);
     }
   }

  double rateAdded = 5;
  var commentController = TextEditingController();
  Map<String,dynamic>? addReviewResponse;
  addReview(id) async {
    String email = await CashHelper.getSavedString("email", "");
    String name = await CashHelper.getSavedString("name", "");
    Map<String,String> formData = {
      "product_id": id.toString(),
      "review": commentController.text,
      "reviewer": name,
      "reviewer_email": email,
      "rating": rateAdded.toString()
    };
    emit(AddReviewsLoadingState());
    try{
      addReviewResponse = await CategoriesRepository.addReview(formData);
      fetchProductsReview(id);
      emit(AddReviewsLoadedState());
    }catch(e){
      emit(AddReviewsErrorState());
      return Future.error(e);
    }
  }


  List<BannerModel> bannerModel = [];
  fetchBanner() async {
    try {
      List response = await CategoriesRepository.fetchBanner();
      for (var element in response) {
        bannerModel.add(BannerModel.fromJson(element));
      }

    } catch (e) {
      return Future.error(e);
    }
  }

  List<List<ProductModel>> homeMenuModel = [];
  List<Link> linkList = [];
  fetchHomeMenu({required catId , required page , required perPage,colorFilter,sizeFilter,minPrice,maxPrice,ratingCount, String? name})async{
    favList = favProducts;
    if(page == 1) {
      homeMenuModel.clear();
      emit(ProductsLoadingState());
    }else{
      emit(ProductsLoadingPaginateState());
    }
    try{
      List responseCat = await CategoriesRepository.fetchHomeMenu();
      for (int i = 0; i<responseCat.length; i++) {
        if(responseCat[i]['link']['object'] == "product_cat") {
          linkList.add(Link.fromJson(responseCat[i]['link']));
          homeMenuModel.add([]);
          responseCat[i]['products'].forEach((element){
            homeMenuModel[i].add(ProductModel.fromJson(element));
          });
      }
      }
      emit(ProductsLoadedState());

    }catch(e){
      emit(ProductsErrorState());
      return Future.error(e);
    }
  }

  List<SizeGuideModel> sizeGuideList = [];
  fetchSizeGuide(productId)async{
    sizeGuideList.clear();
  try {
    emit(SizeGuideLoadingState());
    List response = await CategoriesRepository.fetchSizeGuide(productId);
    for (var element in response) {
      sizeGuideList.add(SizeGuideModel.fromJson(element));
    }
    if(sizeGuideList.isNotEmpty){
      emit(SizeGuideLoadedState());
    }else{
      emit(SizeGuideEmptyState());
    }
  } catch (e) {
    emit(SizeGuideErrorState());
    return Future.error(e);
  }
}

  List<Attributes> attributes = [];
  var sizeController = [];
  var slugController = [];

  fetchAttributes() async {
    attributes.clear();
  try {
    // emit(SizeGuideLoadingState());
    List response = await CategoriesRepository.fetchAttributes();
    for (var element in response) {
      if(element['id']==35 || element['id'] == 28 || element['id'] == 43 || element['id'] == 15 || element['id'] == 32) {
        attributes.add(Attributes.fromJson(element));
        sizeController.add(TextEditingController());
        slugController.add(TextEditingController());
      }

    }
    // if(attributes.isNotEmpty){
    //   // emit(SizeGuideLoadedState());
    // }else{
    //   // emit(SizeGuideEmptyState());
    // }
  } catch (e) {
    // emit(SizeGuideErrorState());
    return Future.error(e);
  }
}

  List<Attributes> attributeTerms = [];
  fetchAttributeTerms(id) async {
    attributeTerms.clear();
  try {
    // emit(SizeGuideLoadingState());
    List response = await CategoriesRepository.fetchAttributeTerms(id);
    for (var element in response) {
      attributeTerms.add(Attributes.fromJson(element));
    }
    // if(attributes.isNotEmpty){
    //   // emit(SizeGuideLoadedState());
    // }else{
    //   // emit(SizeGuideEmptyState());
    // }
  } catch (e) {
    // emit(SizeGuideErrorState());
    return Future.error(e);
  }
}

}
