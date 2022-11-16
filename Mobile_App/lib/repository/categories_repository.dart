import 'dart:convert';

import '../shared/network_helper.dart';

class CategoriesRepository {

  static Future fetchCategories() async {
    return await NetworkHelper.repo("wp-json/wc/v3/products/categories?per_page=100&","get",headerState: false);
  }

  static Future fetchHomeMenu() async {
    return await NetworkHelper.repo("wp-json/menu/mobile/app?order_id=87541&","get",headerState: false);
  }

  static Future fetchSizeGuide(productId) async {
    return await NetworkHelper.repo("wp-json/size/guides/product/88641?","get",headerState: false);
  }

  static Future fetchAttributes() async {
    return await NetworkHelper.repo("wp-json/wc/v3/products/attributes?","get",headerState: false);
  }

  static Future fetchAttributeTerms(id) async {
    return await NetworkHelper.repo("wp-json/wc/v3/products/attributes/$id/terms?","get",headerState: false);
  }

  static Future fetchProductsByCategory({required catId , required page , required perPage,filterParams,minPrice,maxPrice,ratingCount,name}) async {
    // if(filterParams==null) {
      return await NetworkHelper.repo("wp-json/wc/v3/products?category=${catId==0?'':catId}&page=$page&per_page=$perPage&${filterParams??''}min_price=${minPrice??''}&max_price=${maxPrice??''}&average_rating=${ratingCount??''}&search=${name??''}&order=desc&","get",headerState: false);
    // }else{
    //   return await NetworkHelper.repo("wp-json/menu/mobile/app?category=${catId==0?'':catId}&page=$page&per_page=$perPage&${filterParams??''}min_price=${minPrice??''}&max_price=${maxPrice??''}&average_rating=${ratingCount??''}&search=${name??''}&order=desc&","get",headerState: false);
    // }
  }

  static Future fetchFavProductsWithApi({required email}) async {
    return await NetworkHelper.repo("wp-json/yith/wishlist/v1/wishlists?email=$email&","get",headerState: false);
  }

  static Future fetchProductVariations(id) async {
    return await NetworkHelper.repo("wp-json/wc/v3/products/$id/variations?","get",headerState: false);
  }

  static Future fetchBanner() async {
    return await NetworkHelper.repo("wp-json/slidermobile/apis/get?","get",headerState: false);
  }

  static Future fetchProductsReview(id) async {
    return await NetworkHelper.repo("wp-json/wc/v2/products/$id/reviews?","get",headerState: false);
  }

  static Future addReview(formData) async {
    return await NetworkHelper.repo("wp-json/wc/v3/products/reviews?","post",formData: formData,headerState: false);
  }

  static Future favProductWithApi({required email, required wishListId, required productId}) async {
    print(wishListId);
    return await NetworkHelper.repo("wp-json/yith/wishlist/v1/wishlists/$wishListId/product/$productId?email=$email&","post",headerState: true);
  }

  static Future unFavProductWithApi({required email, required wishListId, required productId}) async {
    print(wishListId);
    return await NetworkHelper.repo("wp-json/yith/wishlist/v1/wishlists/$wishListId/product/$productId?email=$email&","delete",headerState: true);
  }

  static Future addToCart(cartKey,id,product) async {
    if(cartKey=="") {
      return await NetworkHelper.repo("wp-json/cocart/v2/cart/add-item?id=$id","post",formData: product,headerState: true,key: false);
    }else{
      return await NetworkHelper.repo("wp-json/cocart/v2/cart/add-item?id=$id&cart_key=$cartKey","post",formData: product,headerState: true,key: false);
    }
  }

  static Future fetchCartItems(cartKey) async {
    return await NetworkHelper.repo("wp-json/cocart/v2/cart?cart_key=$cartKey","get",headerState: true,key: false);
  }

  static Future updateCartItem(String cartKey, itemKey, qty) async {
    return await NetworkHelper.repo("wp-json/cocart/v2/cart/item/$itemKey?quantity=$qty","get",headerState: true,key: false);
  }


  static Future deleteItem(String cartKey, itemKey) async {
    return await NetworkHelper.repo("wp-json/cocart/v2/cart/item/$itemKey","delete",headerState: true,key: false);
  }


}