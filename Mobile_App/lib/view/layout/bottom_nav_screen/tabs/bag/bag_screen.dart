import 'dart:convert';

import 'package:ahshiaka/bloc/layout_cubit/categories_cubit/categories_cubit.dart';
import 'package:ahshiaka/bloc/layout_cubit/categories_cubit/categories_states.dart';
import 'package:ahshiaka/bloc/layout_cubit/checkout_cubit/checkout_cubit.dart';
import 'package:ahshiaka/bloc/layout_cubit/checkout_cubit/checkout_state.dart';
import 'package:ahshiaka/shared/components.dart';
import 'package:ahshiaka/utilities/app_ui.dart';
import 'package:ahshiaka/utilities/app_util.dart';
import 'package:ahshiaka/view/layout/bottom_nav_screen/tabs/categories/products/product_details_screen.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:easy_localization/easy_localization.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../../../bloc/layout_cubit/bottom_nav_cubit.dart';
import '../../../../../shared/cash_helper.dart';
import 'checkout/checkout_screen.dart';

class BagScreen extends StatefulWidget {
  const BagScreen({Key? key}) : super(key: key);

  @override
  _BagScreenState createState() => _BagScreenState();
}

class _BagScreenState extends State<BagScreen> {
  @override
  void didChangeDependencies() {
    // TODO: implement didChangeDependencies
    super.didChangeDependencies();
    refreshPage();
  }
  @override
  Widget build(BuildContext context) {
    final cubit = CheckoutCubit.get(context);
    final catCubit = CategoriesCubit.get(context);
    CheckoutCubit.get(context).fetchCartList(context);
    return Padding(
      padding: const EdgeInsets.all(16.0),
      child:BlocBuilder<CheckoutCubit,CheckoutState>(
          buildWhen: (_,state) => state is CheckoutChangeState,
          builder: (context, state) {
            if(cubit.cartList.isEmpty){
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
            return Stack(
            alignment: Alignment.bottomCenter,
            children: [
              SingleChildScrollView(
                child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      SizedBox(height: MediaQuery.of(context).padding.top,),
                      CustomText(text: "myBag".tr(),fontSize: 18,fontWeight: FontWeight.w600,color: AppUI.blackColor,),
                      ListView(
                              shrinkWrap: true,
                              physics: const NeverScrollableScrollPhysics(),
                              children: List.generate(cubit.cartList.length, (index) {
                                return InkWell(
                                  onTap: (){
                                    AppUtil.mainNavigator(context, ProductDetailsScreen(product: cubit.cartList[index]));
                                  },
                                  child: Column(
                                    children: [
                                      Column(
                                        children: [
                                          Row(
                                            children: [
                                              Expanded(
                                                flex: 2,
                                                child: CachedNetworkImage(imageUrl: cubit.cartList[index].image!=null?cubit.cartList[index].image!['src']!:cubit.cartList[index].images!=null && cubit.cartList[index].images!.isNotEmpty?cubit.cartList[index].images![0].src!:"",height: 130,placeholder: (context, url) => Stack(
                                                  children: [
                                                    Image.asset("${AppUI.imgPath}product_background.png",height: 130,fit: BoxFit.fill,),
                                                  ],
                                                ),
                                                  errorWidget: (context, url, error) => Stack(
                                                    children: [
                                                      Image.asset("${AppUI.imgPath}product_background.png",height: 130,fit: BoxFit.fill,),
                                                    ],
                                                  ),),
                                              ),
                                              const SizedBox(width: 7,),

                                              Expanded(
                                                flex: 5,
                                                child: Padding(
                                                  padding: const EdgeInsets.symmetric(horizontal: 3),
                                                  child: Row(
                                                    children: [
                                                      Column(
                                                        crossAxisAlignment: CrossAxisAlignment.start,
                                                        mainAxisAlignment: MainAxisAlignment.center,
                                                        children: [
                                                          // if(cubit.cartModel!.items![index].title!=null)
                                                            SizedBox(
                                                              width: AppUtil.responsiveWidth(context)*0.4,
                                                                child: CustomText(text: cubit.cartList[index].name,color: AppUI.blueColor,)),
                                                          // CustomText(text: cubit.cartModel!.items![index].name!.length<3?cubit.cartModel!.items![index].name:"${cubit.cartModel!.items![index].name!.substring(3,cubit.cartModel!.items![index].name!.length>29?24:cubit.cartModel!.items![index].name!.length-5)}...",color: AppUI.blackColor,),
                                                          const SizedBox(height: 6,),
                                                          Row(
                                                            children: [
                                                              CustomText(text: "${cubit.cartList[index].price} SAR",color:  cubit.cartList[index].salePrice==""?AppUI.blackColor:AppUI.orangeColor,fontWeight: FontWeight.w600,),
                                                              const SizedBox(width: 10,),
                                                              if(cubit.cartList[index].salePrice!="" && cubit.cartList[index].salePrice!=null)
                                                                CustomText(text: "${cubit.cartList[index].salePrice} SAR",color: AppUI.iconColor,textDecoration: TextDecoration.lineThrough,fontSize: 12,),
                                                            ],
                                                          ),
                                                          const SizedBox(height: 5,),
                                                          if(cubit.cartList[index].attributes!.isNotEmpty && cubit.cartList[index].attributes![0].option!=null)
                                                          Row(
                                                            children: [
                                                              CustomText(text: cubit.cartList[index].attributes![0].name,color: AppUI.iconColor,),
                                                              const SizedBox(width: 10,),
                                                              CustomText(text:  cubit.cartList[index].attributes![0].option,color: AppUI.blackColor,),
                                                            ],
                                                          ),
                                                          const SizedBox(height: 10,),
                                                          Row(
                                                            children: [
                                                              InkWell(
                                                                onTap: (){
                                                                  if(cubit.qty[index]!=1) {
                                                                    cubit.changeQuantity(cubit.cartList[index].id, --cubit.qty[index],"decrement",context);
                                                                  }
                                                                },
                                                                child: CircleAvatar(radius: 13,backgroundColor: AppUI.greyColor,child: Padding(
                                                                  padding: const EdgeInsets.all(1.0),
                                                                  child: CircleAvatar(backgroundColor: AppUI.whiteColor,child: const CustomText(text: "-",fontSize: 18,)),
                                                                )),
                                                              ),
                                                              const SizedBox(width: 10,),
                                                              CustomText(text: "${cubit.qty[index]}",fontSize: 22,),
                                                              const SizedBox(width: 10,),
                                                              InkWell(
                                                                onTap: (){
                                                                  cubit.changeQuantity(cubit.cartList[index].id, ++cubit.qty[index],"increment",context);
                                                                },
                                                                child: CircleAvatar(radius: 13,backgroundColor: AppUI.greyColor,child: Padding(
                                                                  padding: const EdgeInsets.all(1.0),
                                                                  child: CircleAvatar(backgroundColor: AppUI.whiteColor,child: const CustomText(text: "+",fontSize: 18,)),

                                                                )),
                                                              ),
                                                              // SizedBox(width: AppUtil.responsiveWidth(context)*0.27,),
                                                            ],
                                                          ),

                                                        ],
                                                      ),
                                                    ],
                                                  ),
                                                ),
                                              )
                                            ],
                                          ),
                                          Padding(
                                            padding: const EdgeInsets.all(15.0),
                                            child: Row(
                                              children: [
                                                InkWell(
                                                    onTap: (){
                                                      cubit.removeCartItemItem(cubit.cartList[index],index,context);
                                                    },
                                                    child: Image.asset("${AppUI.imgPath}trash.png")),
                                                const Spacer(),
                                                BlocBuilder<CategoriesCubit,CategoriesState>(
                                                    buildWhen: (_,state) => state is ChangeFavState,
                                                    builder: (context, state) {
                                                      return InkWell(
                                                          onTap: (){
                                                            if(!cubit.cartList[index].fav!) {
                                                              catCubit
                                                                  .favProduct(
                                                                  cubit
                                                                      .cartList[index],context);
                                                            }else{
                                                              catCubit.removeFromFav(cubit.cartList[index],context);
                                                            }
                                                          },
                                                          child: Row(
                                                            children: [
                                                              Icon(cubit.cartList[index].fav!?Icons.favorite:Icons.favorite_outline,color: AppUI.errorColor,),
                                                              const SizedBox(width: 10,),
                                                              CustomText(text: "addToWishList".tr())
                                                            ],
                                                          ));
                                                    }
                                                ),

                                              ],
                                            ),
                                          ),
                                          const SizedBox(height: 10,)
                                        ],
                                      ),
                                    ],
                                  ),
                                );
                              }),

                      ),

                      BlocBuilder<CheckoutCubit,CheckoutState>(
                        buildWhen: (_,state) => state is CheckoutChangeState || state is ApplyCoupon,
                        builder: (context, state) {
                          return Column(
                            children: [
                              const SizedBox(height: 10,),
                              CustomInput(controller: cubit.couponController,hint: "enterCoupon".tr(), textInputType: TextInputType.text,fillColor: cubit.couponApplied?AppUI.backgroundColor:AppUI.whiteColor,borderColor: AppUI.backgroundColor,radius: 4,suffixIcon: InkWell(
                                  onTap: (){
                                    if(!cubit.couponApplied) {
                                      cubit.applyCoupon(context);
                                    }else{
                                      cubit.cancelCoupon();
                                    }
                                  },
                                  child: CustomText(text: cubit.couponApplied?"cancel".tr():"apply".tr(),color: AppUI.blueColor,)),),

                              const SizedBox(height: 10,),
                              Row(
                                children: [
                                  CustomText(text: "totalPrice".tr().toUpperCase()),
                                  const Spacer(),
                                  CustomText(text: "${cubit.total} SAR",color: AppUI.blackColor,fontSize: 18,fontWeight: FontWeight.w600,)
                                ],
                              ),
                            ],
                          );
                        }
                      ),
                      const SizedBox(height: 10,),
                      CustomButton(text: "checkout".tr(),onPressed: (){
                        AppUtil.mainNavigator(context, const CheckoutScreen());
                      },),
                      const SizedBox(height: 10,),
                      CustomButton(text: "continueShopping".tr(),borderColor: AppUI.mainColor,color: AppUI.whiteColor,textColor: AppUI.mainColor,onPressed: (){
                        BottomNavCubit.get(context).setCurrentIndex(0);
                      },)
                    ],
                  ),
              ),
            ],
          );
        }
      ),
    );
  }

   refreshPage(){
    Future.delayed(const Duration(seconds: 1),(){
      setState(() {});
    });
   }
}
