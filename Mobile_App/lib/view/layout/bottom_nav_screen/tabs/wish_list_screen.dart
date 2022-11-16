import 'package:ahshiaka/bloc/layout_cubit/categories_cubit/categories_cubit.dart';
import 'package:ahshiaka/bloc/layout_cubit/categories_cubit/categories_states.dart';
import 'package:easy_localization/easy_localization.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:shimmer/shimmer.dart';
import '../../../../shared/components.dart';
import '../../../../utilities/app_ui.dart';
import '../../../../utilities/app_util.dart';
import 'home/shimmer/home_shimmer.dart';

class WishListScreen extends StatefulWidget {
  const WishListScreen({Key? key}) : super(key: key);

  @override
  _WishListScreenState createState() => _WishListScreenState();
}

class _WishListScreenState extends State<WishListScreen> {

  @override
  void initState() {
    // TODO: implement initState
    super.initState();
    CategoriesCubit.get(context).fetchFavProducts();
  }
  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.all(16.0),
      child: Stack(
        alignment: Alignment.bottomCenter,
        children: [
          SingleChildScrollView(
            child: Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                SizedBox(height: MediaQuery.of(context).padding.top,),
                CustomText(text: "wishList".tr(),fontSize: 18,fontWeight: FontWeight.w600,color: AppUI.blackColor,),
                BlocBuilder<CategoriesCubit,CategoriesState>(
                  buildWhen: (_,state) => state is ChangeFavState || state is FavLoadingState || state is FavEmptyState || state is FavLoadedState || state is FavErrorState,
                  builder: (context, state) {
                    final cubit = CategoriesCubit.get(context);
                    if(state is FavLoadingState){
                        return Shimmer.fromColors(
                          baseColor: AppUI.shimmerColor, highlightColor: AppUI.whiteColor,direction: AppUtil.rtlDirection(context)?ShimmerDirection.rtl:ShimmerDirection.ltr,
                          child: const ProductsShimmer(type: "list",)
                      );
                    }
                    if(state is FavEmptyState){
                      return Padding(
                        padding: const EdgeInsets.all(16.0),
                        child: Column(
                          mainAxisAlignment: MainAxisAlignment.center,
                          crossAxisAlignment: CrossAxisAlignment.center,
                          children: [
                            Image.asset("${AppUI.imgPath}emptyBag.png",height: 200),
                            const SizedBox(height: 50,width: double.infinity,),
                            CustomText(text: "noProductsFound".tr(),fontSize: 18,),
                            const SizedBox(height: 40,),
                            CustomText(text: "shoppingInOurApp".tr(),textAlign: TextAlign.center,color: AppUI.iconColor,)
                          ],
                        ),
                      );
                    }

                    if(state is FavErrorState){
                      return Center(child: CustomText(text: "error".tr(),fontSize: 24,),);
                    }
                    return ListView(
                      shrinkWrap: true,
                      physics: const NeverScrollableScrollPhysics(),
                      children: List.generate(cubit.favProducts.length, (index) {
                        return Column(
                          children: [
                            ProductCard(type: "list",product: cubit.favProducts[index],onDelete: (){
                              cubit.removeFromFav(cubit.favProducts[index],context);
                            },addToCart: () async {
                              List variant = await cubit.getVariationId(cubit.variations,context,fromFav: true);
                              if(variant[0] == -1){
                                cubit.favProducts[index].qty = 1;
                              }
                              if(variant[0] != 0){
                                for (var element in cubit.variations) {
                                  if(element.id == variant[0]){
                                    element.name = cubit.favProducts[index].name;
                                    element.mainProductId = cubit.favProducts[index].id.toString();
                                    if(!mounted)return;
                                    cubit.addToCart(context, element,slugs: variant[1]);
                                  }
                                }
                              }else{
                                bool exists = await cubit.fetchItemInCart(cubit.favProducts[index].id);
                                if(!exists){
                                  cubit.favProducts[index].qty = 1;
                                }
                                cubit.favProducts[index].mainProductId = cubit.favProducts[index].id.toString();
                                if(!mounted)return;
                                cubit.addToCart(context, cubit.favProducts[index]);
                              }
                              cubit.removeFromFav(cubit.favProducts[index],context);

                            },),
                            // Padding(
                            //   padding: const EdgeInsets.all(8.0),
                            //   child: Row(
                            //     children: [
                                  // InkWell(
                                  //   onTap: (){
                                  //     int variationId = cubit.getVariationId(cubit.favProducts[index]);
                                  //     for (var element in cubit.favProducts[index].variations!) {
                                  //       if(element.id == variationId){
                                  //         cubit.addToCart(context, element);
                                  //       }
                                  //     }
                                  //     },
                                  //   child: Row(
                                  //     children: [
                                  //       Image.asset("${AppUI.imgPath}bag.png",color: AppUI.mainColor,),
                                  //       const SizedBox(width: 5,),
                                  //       CustomText(text: "addToBag".tr(),color: AppUI.mainColor,),
                                  //     ],
                                  //   ),
                                  // ),
                                  // const Spacer(),
                                  // Image.asset("${AppUI.imgPath}edit.png"),
                                  // const SizedBox(width: 10,),
                                  // InkWell(
                                  //   onTap: (){
                                  //         cubit.removeFromFav(cubit.favProducts[index],cubit.productModel);
                                  //   },
                                  //     child: Image.asset("${AppUI.imgPath}trash.png")),
                            //     ],
                            //   ),
                            // ),
                            const SizedBox(height: 5,),
                            const Divider(),
                            const SizedBox(height: 5,)
                          ],
                        );
                      }),
                    );
                  }
                ),
              ],
            ),
          ),
        ],
      ),
    );
  }
}
