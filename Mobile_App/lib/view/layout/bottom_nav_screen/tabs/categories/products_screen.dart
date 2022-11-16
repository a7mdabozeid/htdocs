import 'dart:convert';

import 'package:ahshiaka/bloc/layout_cubit/categories_cubit/categories_cubit.dart';
import 'package:ahshiaka/bloc/layout_cubit/categories_cubit/categories_states.dart';
import 'package:ahshiaka/models/categories/products_model.dart';
import 'package:ahshiaka/shared/components.dart';
import 'package:ahshiaka/utilities/app_ui.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:easy_localization/easy_localization.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:flutter_svg/flutter_svg.dart';
import 'package:range_slider_flutter/range_slider_flutter.dart';
import 'package:shimmer/shimmer.dart';

import '../../../../../utilities/app_util.dart';
import '../home/shimmer/home_shimmer.dart';
class ProductsScreen extends StatefulWidget {
  final int catId;
  final String catName;
  const ProductsScreen({Key? key,required this.catId,required this.catName}) : super(key: key);

  @override
  _ProductsScreenState createState() => _ProductsScreenState();
}

class _ProductsScreenState extends State<ProductsScreen> {
  late CategoriesCubit cubit;

  FilterClass? selectedColor;
  FilterClass? selectedSize;
  var colorController = TextEditingController();
  double lowerPrice=0.0 ,upperPrice=2000.0;

  var groupValue = 0;
  @override
  void initState() {
    // TODO: implement initState
    super.initState();
    cubit = CategoriesCubit.get(context);
    cubit.productPage=1;
    cubit.fetchProductsByCategory(catId: widget.catId, page: cubit.productPage, perPage: 20);
    cubit.productScrollController.addListener(() {
      if (cubit.productScrollController.position.pixels ==
          cubit.productScrollController.position.maxScrollExtent) {
        cubit.productPage++;
        cubit.fetchProductsByCategory(catId: widget.catId, page: cubit.productPage, perPage: 20);
      }
    });
  }
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar:  customAppBar(title: widget.catId == 0?"search".tr():widget.catName,elevation: 0),
      body: Column(
        children: [
          if(widget.catId==0)
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                Padding(
                  padding: const EdgeInsets.symmetric(horizontal: 16),
                  child: CustomInput(controller: cubit.searchController,hint: "searchHere".tr(), textInputType: TextInputType.text,borderColor: Color(0xffFAFAFA),fillColor: Color(0xffFAFAFA),prefixIcon: Image.asset("${AppUI.imgPath}search.png"),onChange: (v){
                    if(v.length>3 || v.isEmpty){
                      cubit.productPage=1;
                      cubit.fetchProductsByCategory(catId: cubit.selectedCatId==0?widget.catId:cubit.selectedCatId, page: cubit.productPage, perPage: 8,name: v);
                    }
                  },),
                ),
                // const SizedBox(height: 10,),
                // Padding(
                //   padding: const EdgeInsets.all(8.0),
                //   child: CustomText(text: "categories".tr(),fontSize: 16,fontWeight: FontWeight.w500,),
                // ),
                // const SizedBox(height: 10,),
                // SizedBox(
                //   height: 135,
                //   child: BlocBuilder<CategoriesCubit,CategoriesState>(
                //       buildWhen: (context,state) => state is CategoriesChangeState,
                //       builder: (context, state) {
                //         if(cubit.categoriesModel.isEmpty){
                //           return Center(child: CustomText(text: "noProductsAvailable".tr(),fontSize: 24,),);
                //         }
                //         return ListView(
                //           scrollDirection: Axis.horizontal,
                //           shrinkWrap: true,
                //           children: List.generate(cubit.categoriesModel.length, (index) {
                //             return Row(
                //               children: [
                //                 InkWell(
                //                   onTap: (){
                //                     cubit.selectedCatId = cubit.categoriesModel[index].id!;
                //                     cubit.productPage=1;
                //                     cubit.fetchProductsByCategory(catId: cubit.selectedCatId==0?widget.catId:cubit.selectedCatId, page: cubit.productPage, perPage: 8,name: cubit.searchController.text.length<3 ? '' : cubit.searchController.text);
                //                     cubit.emit(CategoriesChangeState());
                //                   },
                //                   child: Column(
                //                     children: [
                //                       CircleAvatar(
                //                         radius: 45,
                //                         backgroundColor: cubit.selectedCatId == cubit.categoriesModel[index].id! ? AppUI.mainColor : AppUI.blackColor,
                //                         child: Padding(
                //                           padding: EdgeInsets.all(cubit.selectedCatId == cubit.categoriesModel[index].id! ? 3 :1.0),
                //                           child: CircleAvatar(
                //                             radius: 45,
                //                             backgroundColor: AppUI.whiteColor,
                //                             child: Padding(
                //                               padding: const EdgeInsets.all(2.0),
                //                               child: ClipRRect(
                //                                 borderRadius: BorderRadius.circular(50),
                //                                 child: CachedNetworkImage(imageUrl: cubit.categoriesModel[index].image==null?"":cubit.categoriesModel[index].image!.src!,placeholder: (context, url) => Image.asset("${AppUI.imgPath}story.png",height: 80,width: 80,fit: BoxFit.fill,),
                //                                   errorWidget: (context, url, error) => Image.asset("${AppUI.imgPath}story.png",height: 80,width: 80,fit: BoxFit.fill,),),
                //                               ),
                //                             ),
                //                           ),
                //                         ),
                //                       ),
                //                       const SizedBox(height: 5,),
                //                       CustomText(text: cubit.categoriesModel[index].name)
                //                     ],
                //                   ),
                //                 ),
                //                 const SizedBox(width: 5,)
                //               ],
                //             );
                //           }),
                //         );
                //       }
                //   ),
                // ),
                const SizedBox(height: 10,),
                // Padding(
                //   padding: const EdgeInsets.all(8.0),
                //   child: CustomText(text: "trendyTags".tr(),fontSize: 16,fontWeight: FontWeight.w500,),
                // ),
                // const SizedBox(height: 10,),
                // Row(
                //   children: [
                //     CustomCard(color: AppUI.mainColor,width: 100,height: 40,padding: 4,radius: 25,child: CustomText(text: "Thope",color: AppUI.whiteColor,),onTap: (){
                //       cubit.productPage=1;
                //       cubit.fetchProductsByCategory(catId: cubit.selectedCatId==0?widget.catId:cubit.selectedCatId, page: cubit.productPage, perPage: 8,name: cubit.searchController.text.length<3 ? '' : cubit.searchController.text);
                //     },),
                //     CustomCard(color: AppUI.mainColor,width: 100,height: 40,padding: 4,radius: 25,child: CustomText(text: "Vest",color: AppUI.whiteColor,),onTap: (){
                //       cubit.productPage=1;
                //       cubit.fetchProductsByCategory(catId: cubit.selectedCatId==0?widget.catId:cubit.selectedCatId, page: cubit.productPage, perPage: 8,name: cubit.searchController.text.length<3 ? '' : cubit.searchController.text);
                //     },),
                //   ],
                // )
              ],
            ),
          Column(
            children: [
              const Divider(thickness: 1,),
              Row(
                children: [
                  InkWell(
                    onTap: (){
                      AppUtil.dialog(context, "sort".tr(), [
                        StatefulBuilder(
                          builder: (context, setState) {
                            return Column(
                              children: [
                                SizedBox(width: AppUtil.responsiveWidth(context)*0.99,),
                                InkWell(
                                  onTap: (){
                                    groupValue = 0;
                                    setState((){});
                                  },
                                  child: Row(
                                    children: [
                                      CustomText(text: "name".tr()),
                                      const Spacer(),
                                      Radio(value: 0, groupValue: groupValue,activeColor: AppUI.orangeColor, onChanged: (int? v){
                                        groupValue = v!;
                                        setState(() {});
                                      })
                                    ],
                                  ),
                                ),
                                const SizedBox(height: 10,),
                                const Divider(),
                                const SizedBox(height: 10,),
                                InkWell(
                                  onTap: (){
                                    groupValue = 1;
                                    setState((){});
                                  },
                                  child: Row(
                                    children: [
                                      CustomText(text: "date".tr()),
                                      const Spacer(),
                                      Radio(value: 1, groupValue: groupValue,activeColor: AppUI.orangeColor, onChanged: (int? v){
                                        groupValue = v!;
                                        setState(() {});
                                      })
                                    ],
                                  ),
                                ),
                                const SizedBox(height: 10,),
                                const Divider(),
                                const SizedBox(height: 10,),
                                InkWell(
                                  onTap: (){
                                    groupValue = 2;
                                    setState((){});
                                  },
                                  child: Row(
                                    children: [
                                      CustomText(text: "priceHighToLow".tr()),
                                      const Spacer(),
                                      Radio(value: 2, groupValue: groupValue,activeColor: AppUI.orangeColor, onChanged: (int? v){
                                        groupValue = v!;
                                        setState(() {});
                                      })
                                    ],
                                  ),
                                ),
                                const SizedBox(height: 10,),
                                const Divider(),
                                const SizedBox(height: 10,),
                                InkWell(
                                  onTap: (){
                                    groupValue = 3;
                                    setState((){});
                                  },
                                  child: Row(
                                    children: [
                                      CustomText(text: "priceLowToHigh".tr()),
                                      const Spacer(),
                                      Radio(value: 3, groupValue: groupValue,activeColor: AppUI.orangeColor, onChanged: (int? v){
                                        groupValue = v!;
                                        setState(() {});
                                      })
                                    ],
                                  ),
                                ),

                                // const SizedBox(height: 10,),
                                // const Divider(),
                                // const SizedBox(height: 10,),
                                // InkWell(
                                //   onTap: (){
                                //     groupValue = 4;
                                //     setState((){});
                                //   },
                                //   child: Row(
                                //     children: [
                                //       CustomText(text: "newSeason".tr()),
                                //       const Spacer(),
                                //       Radio(value: 4, groupValue: groupValue,activeColor: AppUI.orangeColor, onChanged: (int? v){
                                //         groupValue = v!;
                                //         setState(() {});
                                //       })
                                //     ],
                                //   ),
                                // ),

                                const SizedBox(height: 10,),
                                const Divider(),
                                const SizedBox(height: 10,),
                                InkWell(
                                  onTap: (){
                                    groupValue = 5;
                                    setState((){});
                                  },
                                  child: Row(
                                    children: [
                                      CustomText(text: "discount".tr()),
                                      const Spacer(),
                                      Radio(value: 5, groupValue: groupValue,activeColor: AppUI.orangeColor, onChanged: (int? v){
                                        groupValue = v!;
                                        setState(() {});
                                      })
                                    ],
                                  ),
                                ),

                                const SizedBox(height: 30,),
                                Row(
                                  mainAxisAlignment: MainAxisAlignment.center,
                                  children: [
                                    CustomButton(text: "submit".tr(),width: AppUtil.responsiveWidth(context)*0.67,onPressed: (){
                                      if(groupValue==0){
                                        cubit.productModel.sort((a, b) => a.name!.toLowerCase().compareTo(b.name!.toLowerCase()));
                                      }
                                      if(groupValue==1){
                                        cubit.productModel.sort((a, b) => a.dateCreated!.toLowerCase().compareTo(b.dateCreated!.toLowerCase()));
                                      }
                                      if(groupValue==3){
                                        cubit.productModel.sort((a, b) => double.parse(a.price==""?"0.0":a.price!).compareTo(double.parse(b.price==""?"0.0":b.price!)));
                                      }
                                      if(groupValue==2){
                                        cubit.productModel.sort((b, a) => double.parse(a.price==""?"0.0":a.price!).compareTo(double.parse(b.price==""?"0.0":b.price!)));
                                      }
                                      if(groupValue==4){
                                        cubit.productModel.sort((b, a) => double.parse(a.price==""?"0.0":a.price!).compareTo(double.parse(b.price==""?"0.0":b.price!)));
                                      }
                                      if(groupValue==5){
                                        cubit.productModel.sort((a, b) => b.salePrice!.compareTo(a.salePrice!));
                                      }
                                      cubit.productScrollController.jumpTo(0);
                                      cubit.emit(ProductsLoadedState());
                                      Navigator.pop(context);
                                    },),
                                    const SizedBox(width: 10,),
                                    CustomButton(text: "reset".tr(),color: AppUI.mainColor.withOpacity(0.2),textColor: AppUI.mainColor,width: AppUtil.responsiveWidth(context)*0.2,onPressed: (){
                                     groupValue = 0;
                                      setState(() {});
                                     cubit.productModel.sort((a, b) => a.name!.toLowerCase().compareTo(b.name!.toLowerCase()));
                                     cubit.emit(ProductsLoadedState());
                                     Navigator.pop(context);
                                    },),
                                  ],
                                )
                              ],
                            );
                          }
                        ),

                      ],alignment: Alignment.bottomCenter);
                    },
                    child: SizedBox(
                      width: AppUtil.responsiveWidth(context)*0.49,
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          SvgPicture.asset("${AppUI.iconPath}sort.svg"),
                          const SizedBox(width: 3,),
                          CustomText(text: "sort".tr())
                        ],
                      ),
                    ),
                  ),
                  Container(height: 30,width: 1.5,color: AppUI.shimmerColor,),
                  InkWell(
                    onTap: (){
                      // cubit.productPage=1;
                      // cubit.fetchProductsByCategory(catId: widget.catId, page: cubit.productPage, perPage: 3,colorFilter: selectedColor!.name);

                      AppUtil.dialog(context, "filter".tr(), [
                        SizedBox(
                          width: AppUtil.responsiveWidth(context)*0.9,
                          child: StatefulBuilder(
                              builder: (context, setState) {
                                return Column(
                                  children: [
                                    const SizedBox(height: 10),
                                    CustomText(text: "price".tr()),
                                    const SizedBox(height: 5),
                                    RangeSliderFlutter(
                                      values: [lowerPrice, upperPrice],
                                      rangeSlider: true,
                                      tooltip: RangeSliderFlutterTooltip(
                                        alwaysShowTooltip: false,
                                      ),
                                      max: 2000,
                                      min: 0,
                                      handlerWidth: 30,
                                      fontSize: 12,
                                      textBackgroundColor:AppUI.orangeColor,
                                      onDragCompleted: (handlerIndex, lowerValue, upperValue){
                                        lowerPrice = lowerValue;
                                        upperPrice = upperValue;
                                        setState(() {});
                                      },
                                    ),
                                    Row(
                                      children: [
                                        CustomText(text: "$lowerPrice SAR",color: AppUI.greyColor,),
                                        const Spacer(),
                                        CustomText(text: "$upperPrice SAR",color: AppUI.greyColor,),
                                      ],
                                    ),
                                    const SizedBox(height: 30,),
                                    BlocBuilder<CategoriesCubit,CategoriesState>(
                                      buildWhen: (_,state) => false,
                                      builder: (context, state) {
                                        return SizedBox(
                                          height: AppUtil.responsiveHeight(context)*0.5,
                                          width: AppUtil.responsiveWidth(context)*0.9,
                                          child: ListView(
                                            shrinkWrap: true,
                                            children: List.generate(cubit.attributes.length, (index) {
                                              return Column(
                                                children: [
                                                  SizedBox(
                                                    width: AppUtil.responsiveWidth(context)*0.9,
                                                    child: CustomInput(controller: cubit.sizeController[index],lable: cubit.attributes[index].name!.tr(),suffixIcon: const Icon(Icons.keyboard_arrow_down),fillColor: AppUI.shimmerColor.withOpacity(0.6), textInputType: TextInputType.name,readOnly: true,onTap: () async {
                                                      print(cubit.attributes[index].toJson());
                                                      AppUtil.dialog2(context, "", [
                                                        const LoadingWidget()
                                                      ]);
                                                              await cubit.fetchAttributeTerms(cubit.attributes[index].id);
                                                      if (!mounted) return;
                                                      Navigator.pop(context);
                                                      AppUtil.dialog2(context, cubit.attributes[index].name, [
                                                        SizedBox(
                                                          height: AppUtil.responsiveHeight(context)*0.7,
                                                          width: AppUtil.responsiveWidth(context)*0.9,
                                                          child: GridView.count(
                                                            shrinkWrap: true,
                                                            crossAxisCount: 3,
                                                            mainAxisSpacing: 10,
                                                            physics: const NeverScrollableScrollPhysics(),
                                                            padding: const EdgeInsets.all(5),
                                                            crossAxisSpacing: 10,
                                                            childAspectRatio: (150/150),
                                                            children: List.generate(cubit.attributeTerms.length, (index2) {
                                                              return  InkWell(
                                                                  onTap: (){
                                                                    cubit.sizeController[index].text = cubit.attributeTerms[index2].name!.tr();
                                                                    selectedSize = FilterClass(name: cubit.attributes[index].slug);
                                                                    selectedColor = FilterClass(name: cubit.attributeTerms[index2].id!.toString());
                                                                    // cubit.slugController[index].text = cubit.attributeTerms[index].sl;
                                                                    Navigator.pop(context);
                                                                    cubit.emit(ProductsLoadedState());
                                                                  },
                                                                  child:  SizedBox(
                                                                      width: AppUtil.responsiveWidth(context)*0.16,
                                                                      child: CustomCard(height: 55,child: CustomText(text: cubit.attributeTerms[index2].name!.tr()),)));
                                                            }),
                                                          ),
                                                        ),

                                                      ]);
                                                    },),
                                                  ),
                                                  const SizedBox(height: 15,)
                                                ],
                                              );
                                            }),
                                          ),
                                        );
                                      }
                                    ),
                                    const SizedBox(height: 25,),
                                    Row(
                                      mainAxisAlignment: MainAxisAlignment.center,
                                      children: [
                                        CustomButton(text: "submit".tr(),width: AppUtil.responsiveWidth(context)*0.67,onPressed: (){
                                          cubit.productPage=1;
                                          String filterParams = "";
                                          if(selectedSize!=null) {
                                            filterParams= "attribute=${selectedSize!.name}&attribute_term=${selectedColor!.name}&";
                                          }
                                          // for (int i = 0 ; i < cubit.sizeController.length ; i++) {
                                          //   filterParams += "${jsonEncode("attributes[${cubit.attributes[i].slug}]")}=${jsonEncode(cubit.sizeController[i].text)}&";
                                          // }
                                          cubit.fetchProductsByCategory(catId: widget.catId, page: cubit.productPage, perPage: 20,filterParams: filterParams,minPrice: lowerPrice,maxPrice: upperPrice);
                                          Navigator.pop(context);
                                        },),
                                        const SizedBox(width: 10,),
                                        CustomButton(text: "reset".tr(),color: AppUI.mainColor.withOpacity(0.2),textColor: AppUI.mainColor,width: AppUtil.responsiveWidth(context)*0.2,onPressed: (){
                                          upperPrice = 2000;
                                          lowerPrice = 0;
                                          // cubit.sizeController.text = '';
                                          for (var element in cubit.sizeController) {
                                            element.clear();
                                          }
                                          for (var element in cubit.slugController) {
                                            element.clear();
                                          }

                                          colorController.text = '';
                                          setState(() {});
                                          cubit.fetchProductsByCategory(catId: widget.catId, page: cubit.productPage, perPage: 20,minPrice: lowerPrice,maxPrice: upperPrice);
                                          List<ProductModel> pro = cubit.productModel;
                                          cubit.productModel.clear();
                                          for (var element in cubit.attributes) {
                                            for (var p in pro) {
                                              for (var attr in p.attributes!) {
                                                if(element.name == attr.name){
                                                  cubit.productModel.add(p);
                                                }
                                              }
                                            }
                                          }
                                          cubit.emit(ProductsLoadedState());
                                          // Navigator.pop(context);
                                        },),
                                      ],
                                    )
                                  ],
                                );
                              }
                          ),
                        ),

                      ],alignment: Alignment.bottomCenter);
                    },
                    child: SizedBox(
                      width: AppUtil.responsiveWidth(context)*0.49,
                      child: Row(
                        mainAxisAlignment: MainAxisAlignment.center,
                        children: [
                          SvgPicture.asset("${AppUI.iconPath}filter.svg"),
                          const SizedBox(width: 3,),
                          CustomText(text: "filter".tr())
                        ],
                      ),
                    ),
                  ),
                ],
              ),
              const Divider(thickness: 1,),
            ],
          ),
          Expanded(
            child: Padding(
              padding: const EdgeInsets.all(16.0),
              child: BlocBuilder<CategoriesCubit,CategoriesState>(
                buildWhen: (_,state) => state is ProductsLoadingState || state is ProductsLoadingPaginateState || state is ProductsEmptyState || state is ProductsErrorState || state is ProductsLoadedState || state is ChangeFavState,
                builder: (context, state) {
                    if(state is ProductsLoadingState){
                    return Shimmer.fromColors(
                        baseColor: AppUI.shimmerColor, highlightColor: AppUI.whiteColor,direction: AppUtil.rtlDirection(context)?ShimmerDirection.rtl:ShimmerDirection.ltr,
                        child: const ProductsShimmer()
                    );
                  }
                  if(state is ProductsEmptyState){
                    return Center(child: CustomText(text: "noProductsAvailable".tr(),fontSize: 24,),);
                  }

                  if(state is ProductsErrorState){
                    return Center(child: CustomText(text: "error".tr(),fontSize: 24,),);
                  }
                    return Stack(
                    alignment: Alignment.bottomCenter,
                    children: [
                      GridView.count(
                        controller: cubit.productScrollController,
                        crossAxisCount: 2,
                        mainAxisSpacing: 10,
                        padding: const EdgeInsets.all(5),
                        crossAxisSpacing: 10,
                        childAspectRatio: (160/310),
                        children: List.generate(cubit.productModel.length, (index) {
                          return ProductCard(product: cubit.productModel[index],onFav: (){
                            cubit.favProduct(cubit.productModel[index],context);
                          },);
                        }),
                      ),
                      SizedBox(
                        height: state is ProductsLoadingPaginateState ? 90 : 0,
                        width: double.infinity,
                        child: Center(
                          child: cubit.productPage == cubit.productModel.length
                              ? const Text("No More Data")
                              : const LoadingWidget(),
                        ),
                      )
                    ],
                  );
                }
              ),
            ),
          )
        ],
      ),
    );
  }
}

class FilterClass{
  String? name;
  Color? color;

  FilterClass({this.name, this.color});
}