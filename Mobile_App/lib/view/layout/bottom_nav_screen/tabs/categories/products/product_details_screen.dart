import 'package:ahshiaka/models/categories/products_model.dart';
import 'package:ahshiaka/utilities/app_ui.dart';
import 'package:ahshiaka/utilities/app_util.dart';
import 'package:ahshiaka/view/layout/bottom_nav_screen/tabs/categories/products/size_guide_screen.dart';
import 'package:ahshiaka/view/layout/bottom_nav_screen/tabs/categories/products/tabs/details.dart';
import 'package:ahshiaka/view/layout/bottom_nav_screen/tabs/categories/products/tabs/info_and_care.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:easy_localization/easy_localization.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:flutter_rating_bar/flutter_rating_bar.dart';
import 'package:flutter_share/flutter_share.dart';
import 'package:light_carousel/main/light_carousel.dart';
import 'package:string_similarity/string_similarity.dart';

import '../../../../../../bloc/layout_cubit/categories_cubit/categories_cubit.dart';
import '../../../../../../bloc/layout_cubit/categories_cubit/categories_states.dart';
import '../../../../../../shared/components.dart';
import '../products_screen.dart';

class ProductDetailsScreen extends StatefulWidget {
  final ProductModel product;
  const ProductDetailsScreen({Key? key,required this.product}) : super(key: key);

  @override
  _ProductDetailsScreenState createState() => _ProductDetailsScreenState();
}

class _ProductDetailsScreenState extends State<ProductDetailsScreen> {
  CategoriesCubit? cubit;
  ProductModel? product = ProductModel();
  List<List<bool>> optionVisibility= [];
  @override
  void initState() {
    // TODO: implement initState
    super.initState();
    product = widget.product;
    product!.attributes!.removeWhere((element) => element.variation==null?false:!element.variation!);
    fetchAttributes();
    CategoriesCubit.get(context).fetchProductsReview(product!.id);
    CategoriesCubit.get(context).fetchRelatedProducts(catId: product!.categories==null?0:product!.categories[0]['id'], page: 1, perPage: 15);
  }
  @override
  Widget build(BuildContext context) {
    cubit = CategoriesCubit.get(context);
    return Scaffold(
      body: SingleChildScrollView(
        child: Column(
          children: [
            Container(
              color: AppUI.backgroundColor,
              height: AppUtil.responsiveHeight(context)*0.55,
              child: Stack(
                alignment: Alignment.center,
                children: [
                  if(product!.images!=null && product!.images!.isNotEmpty)
                  SizedBox(
                      height: AppUtil.responsiveHeight(context)*0.5,
                      width: double.infinity,
                      child: LightCarousel(
                        images: List.generate(product!.images!.length, (index){
                          return Column(
                            children: [
                              SizedBox(height: AppUtil.responsiveHeight(context)*0.05,),
                              ClipRRect(
                                borderRadius: BorderRadius.circular(10),
                                child: CachedNetworkImage(imageUrl: product!.image!=null?product!.image!['src']!:product!.images!=null && product!.images!.isNotEmpty?product!.images![0].src!:"",height: AppUtil.responsiveHeight(context)*0.4,fit: BoxFit.fill,placeholder: (context, url) => Image.asset("${AppUI.imgPath}men.png",height: AppUtil.responsiveHeight(context)*0.4,width: double.infinity,fit: BoxFit.fill,),
                                  errorWidget: (context, url, error) => Image.asset("${AppUI.imgPath}men.png",height: AppUtil.responsiveHeight(context)*0.4,width: double.infinity,fit: BoxFit.fill,),),
                              ),
                              SizedBox(height: AppUtil.responsiveHeight(context)*0.05,),
                            ],
                          );
                        }),
                        dotSize: 7.0,
                        dotSpacing: 25.0,
                        dotColor: AppUI.mainColor.withOpacity(0.3),
                        dotPosition: AppUtil.rtlDirection(context)?DotPosition.bottomRight:DotPosition.bottomLeft,
                        dotHorizontalPadding: 20,
                        dotIncreasedColor: AppUI.mainColor,
                        indicatorBgPadding: 0.0,
                        dotBgColor: Colors.purple.withOpacity(0.0),
                        borderRadius: true,
                      )
                  ),
                  Positioned(
                    top: MediaQuery.of(context).padding.top,left: AppUtil.rtlDirection(context)?null:20,right: AppUtil.rtlDirection(context)?20:null,
                    child: InkWell(
                      onTap: (){
                        Navigator.pop(context);
                      },
                      child: CircleAvatar(
                        backgroundColor: AppUI.whiteColor,
                        child: Icon(AppUtil.rtlDirection(context)?Icons.arrow_back_ios:Icons.arrow_back_ios_new,color: AppUI.blackColor,size: 19,),
                      ),
                    ),
                  ),
                  if(product!.salePrice!="")
                    Positioned(
                    top:  MediaQuery.of(context).padding.top+30,left: !AppUtil.rtlDirection(context)?null:20,right: !AppUtil.rtlDirection(context)?20:null,
                    child: Stack(
                      alignment: Alignment.center,
                      children: [
                        CustomCard(
                          height: 30,width: 50,elevation: 0,radius: 5,
                          color: AppUI.errorColor.withOpacity(0.13),
                          child: const SizedBox(),
                        ),
                        CustomText(text: "${int.parse((100-(int.parse(product!.salePrice!)/int.parse(product!.regularPrice==""?product!.price!:product!.regularPrice!))*100).round().toString())}%",color: AppUI.errorColor,fontSize: 10,)
                      ],
                    ),
                  ),

                  Positioned(
                    bottom: 30,left: !AppUtil.rtlDirection(context)?null:20,right: !AppUtil.rtlDirection(context)?20:null,
                    child: Column(
                      children: [
                        // CircleAvatar(
                        //   backgroundColor: AppUI.whiteColor,
                        //   child: Image.asset("${AppUI.imgPath}bag.png",color: AppUI.blackColor,),
                        // ),
                        const SizedBox(height: 20,),
                        BlocBuilder<CategoriesCubit,CategoriesState>(
                            buildWhen: (context,state){
                              return state is ChangeFavState;
                            },
                            builder: (context, state) {
                            return InkWell(
                              onTap: (){
                                cubit!.favProduct(product!,context);
                              },
                              child: CircleAvatar(
                                backgroundColor: AppUI.whiteColor,
                                child: Icon(product!.fav!?Icons.favorite:Icons.favorite_border,color: product!.fav!?AppUI.errorColor:AppUI.blackColor,size: 19,),
                              ),
                            );
                          }
                        ),
                        const SizedBox(height: 20,),
                        InkWell(
                          onTap: () async {
                            await FlutterShare.share(
                                title: product!.name!,
                                linkUrl: product!.permalink!,
                                chooserTitle: 'Share ${product!.name}'
                            );
                          },
                          child: CircleAvatar(
                            backgroundColor: AppUI.whiteColor,
                            child: Icon(Icons.share,color: AppUI.blackColor,size: 19,),
                          ),
                        ),
                      ],
                    ),
                  ),
                ],
              ),
            ),
            Padding(
              padding: const EdgeInsets.all(16.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  CustomText(text: product!.name,color: AppUI.iconColor.withOpacity(0.8),fontSize: 13,),
                  Row(
                    children: [
                      CustomText(text: "${product!.salePrice==""?product!.price:product!.salePrice} SAR",color: product!.salePrice=="" ? AppUI.mainColor : AppUI.orangeColor,fontWeight: FontWeight.w600,fontSize: 20,),
                      const SizedBox(width: 15,),
                      if(product!.salePrice!="")
                        CustomText(text: "${product!.price} SAR",color: AppUI.iconColor,textDecoration: TextDecoration.lineThrough,),
                    ],
                  ),
                  SizedBox(
                    width: AppUtil.responsiveWidth(context)*0.9,
                      child: CustomText(text: product!.description!.length<3?product!.description:product!.description!.substring(3,product!.description!.length-5),color: AppUI.blackColor,fontSize: 16,)),
                  if(product!.attributes!.isNotEmpty&&product!.attributes![0].options!=null || product!.attributes!.isEmpty)
                    BlocBuilder<CategoriesCubit,CategoriesState>(
                    buildWhen: (_,state) => state is ReviewsLoadedState,
                    builder: (context, state) {
                      return Row(
                        children: [
                          RatingBar.builder(
                            initialRating: double.parse(cubit!.averageCount.toString()),
                            minRating: 1,
                            direction: Axis.horizontal,
                            allowHalfRating: true,
                            itemCount: 5,
                            ignoreGestures: true,
                            itemSize: 18,
                            unratedColor: AppUI.iconColor.withOpacity(0.1),
                            onRatingUpdate: (rating) {
                              // cubit.setRate(rating);
                            },
                            itemBuilder: (BuildContext context, int index) {return const Icon(Icons.star,size: 30,color: Colors.amber,) ; },
                          ),
                          const SizedBox(width: 10,),
                          CustomText(text: "(${cubit!.averageCount.round()})",fontSize: 12,),
                        ],
                      );
                    }
                  ),
                  const SizedBox(height: 15,),
                  // Row(
                  //   children: [
                  //     CustomText(text: "${"color".tr()}: ",color: AppUI.iconColor,),
                  //     const CustomText(text: "Black",color: Colors.black,fontWeight: FontWeight.w500,),
                  //   ],
                  // ),
                  // const SizedBox(height: 5,),
                  // BlocBuilder<ProductCubit,ProductStates>(
                  //   builder: (context, state) {
                  //   final cubit = ProductCubit.get(context);
                  //     return SizedBox(
                  //       height: 80,
                  //       child: ListView(
                  //         shrinkWrap: true,
                  //         scrollDirection: Axis.horizontal,
                  //         children: List.generate(3, (index) {
                  //           return Row(
                  //             children: [
                  //               CustomCard(
                  //                 onTap: (){
                  //                   cubit.setSelectedColorIndex(index);
                  //                 },
                  //                 elevation: 0,height: 80,width: 60,radius: 10,padding: 0.0,
                  //                 border: cubit.selectedColorIndex==index?AppUI.mainColor:null,color: AppUI.backgroundColor,
                  //                 child: Image.asset("${AppUI.imgPath}men.png"),
                  //               ),
                  //               const SizedBox(width: 10,),
                  //             ],
                  //           );
                  //         }),
                  //       ),
                  //     );
                  //   }
                  // ),
                  // const SizedBox(height: 15,),
                  BlocBuilder<CategoriesCubit,CategoriesState>(
                    builder: (context, state) {
                      cubit = CategoriesCubit.get(context);
                      return SizedBox(
                        width: AppUtil.responsiveWidth(context)*0.9,
                        child: ListView(
                          shrinkWrap: true,
                          physics: const NeverScrollableScrollPhysics(),
                          children: List.generate(product!.attributes!.length, (mainIndex) {
                            return
                              Column(
                              crossAxisAlignment: CrossAxisAlignment.start,
                              children: [
                                Row(
                                  children: [
                                    if(product!.attributes![mainIndex].options!=null)
                                    CustomText(text: "${product!.attributes![mainIndex].name}: ${cubit!.selectedAttributeIndex.isEmpty ?product!.attributes![mainIndex].options![0]:cubit!.selectedAttributeIndex[mainIndex]['name']}",color: AppUI.iconColor,)
                                    else
                                      CustomText(text: "${product!.attributes![mainIndex].name}: ${cubit!.selectedAttributeIndex.isEmpty ?product!.attributes![mainIndex].option:cubit!.selectedAttributeIndex[mainIndex]['name']}",color: AppUI.iconColor,),
                                    const Spacer(),
                                    if(product!.attributes![mainIndex].name == "Size")
                                      InkWell(
                                        onTap: (){
                                          AppUtil.mainNavigator(context, SizeGuideScreen(title: product!.name,options: product!.attributes![mainIndex].options ?? [product!.attributes![mainIndex].option!],productId: product!.id.toString(),));
                                        },
                                          child: CustomText(text: "sizeGuide".tr(),fontSize: 12,color: AppUI.mainColor,))
                                  ],
                                ),
                                const SizedBox(height: 7,),
                                SizedBox(
                                  height: 40,
                                  child: ListView(
                                    shrinkWrap: true,
                                    scrollDirection: Axis.horizontal,
                                    children: List.generate(product!.attributes![mainIndex].options!=null?product!.attributes![mainIndex].options!.length:1, (index) {
                                      if(!optionVisibility[mainIndex][index]){
                                        return const SizedBox();
                                      }
                                      return Row(
                                        children: [
                                          CustomCard(
                                            onTap: (){
                                              cubit!.setSelectedAttributesIndex(index,mainIndex,product!.attributes![mainIndex].options!=null?product!.attributes![mainIndex].options![index]:product!.attributes![mainIndex].option,product!.attributes![mainIndex].id!);
                                              int i = 0;
                                              for (var element in optionVisibility[1]) {
                                                optionVisibility[1].insert(i, false);
                                                optionVisibility[1].removeAt(i+1);
                                                i++;
                                              }
                                              print(optionVisibility[1]);
                                              if(product!.attributes!.length>=2) {
                                                for (var element in cubit!.variations) {
                                                  int y = 0;
                                                  for (var element2 in element.attributes!) {
                                                    if(y>0){
                                                      for (int i=0; i< product!.attributes![1].options!.length; i++) {
                                                        if(StringSimilarity.compareTwoStrings(element2.option!.toLowerCase(),product!.attributes![1].options![i].toLowerCase())>0.4 && element2.option!.length >= product!.attributes![1].options![i].length  && cubit!.selectedAttributeIndex[0]['name'] == element.attributes![y-1].option){
                                                          optionVisibility[1][i] = true;
                                                        }
                                                      }
                                                    }
                                                    y++;
                                                  }
                                                }
                                              }
                                            },
                                            elevation: 0,height: 35,width: -1,radius: 10,padding: 0.0,
                                            border: cubit!.selectedAttributeIndex[mainIndex]['index']==index?AppUI.mainColor:null,color: AppUI.backgroundColor,
                                            child: Padding(
                                              padding: const EdgeInsets.symmetric(horizontal: 20),
                                              child: CustomText(text: product!.attributes![mainIndex].options!=null?product!.attributes![mainIndex].options![index]:product!.attributes![mainIndex].option),
                                            ),
                                          ),
                                          const SizedBox(width: 10,),
                                        ],
                                      );
                                    }),
                                  ),
                                ),
                                const SizedBox(height: 20,)
                              ],
                            );
                          }),
                        ),
                      );
                    }
                  ),
                ],
              ),
            ),
            Container(
              height: 30,color: AppUI.backgroundColor,width: AppUtil.responsiveWidth(context),
            ),
            SizedBox(
              height: 200,
              child: DefaultTabController(
                length: 2,
                initialIndex:  0,
                child: Column(
                  crossAxisAlignment: CrossAxisAlignment.start,
                  children: [
                    TabBar(
                        indicatorWeight: 2,
                        indicatorColor: AppUI.mainColor,
                        unselectedLabelColor: AppUI.blackColor,
                        labelColor: AppUI.mainColor,
                        isScrollable: true,
                        padding: const EdgeInsets.symmetric(horizontal: 10),
                        physics: const BouncingScrollPhysics(),
                        tabs: <Widget>[
                          Tab(child: Text("details".tr(),style: const TextStyle(fontWeight: FontWeight.w100,),textAlign: TextAlign.center,),),
                          Tab(child: Text("infoAndCare".tr(),style: const TextStyle(fontWeight: FontWeight.w100,),textAlign: TextAlign.center,),),
                        ]
                    ),

                    Expanded(
                      child: TabBarView(
                          children: <Widget> [
                            Details(product: product!),
                            InfoAndCare(product: product),
                          ]),
                    ),
                  ],
                ),
              ),
            ),
            Container(
              height: 30,color: AppUI.backgroundColor,width: AppUtil.responsiveWidth(context),
            ),
            if(product!.attributes!.isNotEmpty&&product!.attributes![0].options!=null || product!.attributes!.isEmpty)
            Column(
              crossAxisAlignment: CrossAxisAlignment.start,
              children: [
                const SizedBox(height: 20,),
                BlocBuilder<CategoriesCubit,CategoriesState>(
                    buildWhen: (_,state) => state is ReviewsLoadingState || state is ReviewsLoadedState || state is ReviewsErrorState || state is ReviewsEmptyState,
                    builder: (context, state) {
                      if(state is ReviewsLoadingState) {
                        return const LoadingWidget();
                      }
                      if(state is ReviewsErrorState) {
                        return Center(child: CustomText(
                          text: "errorFetch".tr(), fontSize: 18,));
                      }
                      if(state is ReviewsEmptyState) {
                        return Center(child: CustomText(text: "noReviewsAvailable".tr(),fontSize: 18,));
                      }

                      return Padding(
                        padding: const EdgeInsets.all(16.0),
                        child: Column(
                          crossAxisAlignment: CrossAxisAlignment.start,
                          children: [
                            CustomText(text: "rating".tr()),
                            const Divider(thickness: 1,),
                            const SizedBox(height: 10,),
                            Row(
                              children: [
                                Column(
                                  children: [
                                    CustomText(text: cubit!.averageCount.round().toString(),fontSize: 30,fontWeight: FontWeight.w600,),
                                    CustomText(text: "${cubit!.reviewsModel.length} ${"rating".tr()}",),
                                  ],
                                ),
                                const SizedBox(width: 30,),
                                Column(
                                  children: [
                                    SizedBox(
                                      width: AppUtil.responsiveWidth(context)*0.7,
                                      child: Row(
                                        children: [
                                          const CustomText(text: "5",fontSize: 18,),
                                          const SizedBox(width: 2,),
                                          Icon(Icons.star,color: AppUI.ratingColor,),
                                          const SizedBox(width: 7,),
                                          Expanded(
                                            flex: 9,
                                            child: ClipRRect(
                                                borderRadius: const BorderRadius.all(Radius.circular(30)),
                                                child: LinearProgressIndicator(valueColor: AlwaysStoppedAnimation<Color>(AppUI.ratingColor),minHeight: 12,value: cubit!.reviewsModel.isEmpty?0:cubit!.rating5/cubit!.reviewsModel.length,backgroundColor: AppUI.backgroundColor,)),
                                          ),
                                          const SizedBox(width: 10,),
                                          Expanded(flex: 3,child: CustomText(text: cubit!.reviewsModel.isEmpty?"0 %":"${((cubit!.rating5/cubit!.reviewsModel.length)*100).round()} %",fontSize: 18,)),
                                        ],
                                      ),
                                    ),
                                    SizedBox(
                                      width: AppUtil.responsiveWidth(context)*0.7,
                                      child: Row(
                                        children: [
                                          CustomText(text: "4",fontSize: 18,),
                                          const SizedBox(width: 2,),
                                          Icon(Icons.star,color: AppUI.ratingColor,),
                                          const SizedBox(width: 7,),
                                          Expanded(
                                            flex: 9,
                                            child: ClipRRect(
                                                borderRadius: const BorderRadius.all(Radius.circular(30)),
                                                child: LinearProgressIndicator(valueColor: AlwaysStoppedAnimation<Color>(AppUI.ratingColor),minHeight: 12,value: cubit!.reviewsModel.isEmpty?0:cubit!.rating4/cubit!.reviewsModel.length,backgroundColor: AppUI.backgroundColor,)),
                                          ),
                                          const SizedBox(width: 10,),
                                          Expanded(flex: 3,child: CustomText(text: cubit!.reviewsModel.isEmpty?"0 %":"${((cubit!.rating4/cubit!.reviewsModel.length)*100).round()} %",fontSize: 18,)),
                                        ],
                                      ),
                                    ),
                                    SizedBox(
                                      width: AppUtil.responsiveWidth(context)*0.7,
                                      child: Row(
                                        children: [
                                          const CustomText(text: "3",fontSize: 18,),
                                          const SizedBox(width: 2,),
                                          Icon(Icons.star,color: AppUI.ratingColor,),
                                          const SizedBox(width: 7,),
                                          Expanded(
                                            flex: 9,
                                            child: ClipRRect(
                                                borderRadius: const BorderRadius.all(Radius.circular(30)),
                                                child: LinearProgressIndicator(valueColor: AlwaysStoppedAnimation<Color>(AppUI.ratingColor),minHeight: 12,value: cubit!.reviewsModel.isEmpty?0:cubit!.rating3/cubit!.reviewsModel.length,backgroundColor: AppUI.backgroundColor,)),
                                          ),
                                          const SizedBox(width: 10,),
                                          Expanded(flex: 3,child: CustomText(text: cubit!.reviewsModel.isEmpty?"0 %":"${((cubit!.rating3/cubit!.reviewsModel.length)*100).round()} %",fontSize: 18,)),
                                        ],
                                      ),
                                    ),
                                    SizedBox(
                                      width: AppUtil.responsiveWidth(context)*0.7,
                                      child: Row(
                                        children: [
                                          const CustomText(text: "2",fontSize: 18,),
                                          const SizedBox(width: 2,),
                                          Icon(Icons.star,color: AppUI.ratingColor,),
                                          const SizedBox(width: 7,),
                                          Expanded(
                                            flex: 9,
                                            child: ClipRRect(
                                                borderRadius: const BorderRadius.all(Radius.circular(30)),
                                                child: LinearProgressIndicator(valueColor: AlwaysStoppedAnimation<Color>(AppUI.ratingColor),minHeight: 12,value: cubit!.reviewsModel.isEmpty?0:cubit!.rating2/cubit!.reviewsModel.length,backgroundColor: AppUI.backgroundColor,)),
                                          ),
                                          const SizedBox(width: 10,),
                                          Expanded(flex: 3,child: CustomText(text: cubit!.reviewsModel.isEmpty?"0 %":"${((cubit!.rating2/cubit!.reviewsModel.length)*100).round()} %",fontSize: 18,)),
                                        ],
                                      ),
                                    ),
                                    SizedBox(
                                      width: AppUtil.responsiveWidth(context)*0.7,
                                      child: Row(
                                        children: [
                                          const CustomText(text: "1",fontSize: 18,),
                                          const SizedBox(width: 2,),
                                          Icon(Icons.star,color: AppUI.ratingColor,),
                                          const SizedBox(width: 7,),
                                          Expanded(
                                            flex: 9,
                                            child: ClipRRect(
                                                borderRadius: const BorderRadius.all(Radius.circular(30)),
                                                child: LinearProgressIndicator(valueColor: AlwaysStoppedAnimation<Color>(AppUI.ratingColor),minHeight: 12,value: cubit!.reviewsModel.isEmpty?0:cubit!.rating1/cubit!.reviewsModel.length,backgroundColor: AppUI.backgroundColor,)),
                                          ),
                                          const SizedBox(width: 10,),
                                          Expanded(flex: 3,child: CustomText(text: cubit!.reviewsModel.isEmpty?"0 %":"${((cubit!.rating1/cubit!.reviewsModel.length)*100).round()} %",fontSize: 18,)),
                                        ],
                                      ),
                                    ),

                                  ],
                                )
                              ],
                            ),

                            const SizedBox(height: 10,),
                            const Divider(thickness: 1,),
                            const SizedBox(height: 10,),
                            CustomText(text: "${"reviews".tr()} (${cubit!.reviewsModel.length})",color: AppUI.blackColor,fontSize: 16,fontWeight: FontWeight.w700,),
                            const SizedBox(height: 10,),

                            Column(
                              children: List.generate(cubit!.reviewsModel.length, (index) {
                                return Column(
                                  crossAxisAlignment: CrossAxisAlignment.start,
                                  children: [
                                    RatingBar.builder(
                                      initialRating: double.parse(cubit!.reviewsModel[index].rating.toString()),
                                      minRating: 1,
                                      direction: Axis.horizontal,
                                      itemPadding: const EdgeInsets.symmetric(horizontal: 4),
                                      itemCount: 5,
                                      ignoreGestures: true,
                                      itemSize: 22,
                                      unratedColor: AppUI.mainColor.withOpacity(0.1),
                                      onRatingUpdate: (rating) {
                                        // cubit.setRate(rating);
                                      },
                                      itemBuilder: (BuildContext context, int index) {return const Icon(Icons.star,size: 30,color: Colors.amber,) ; },
                                    ),
                                    const SizedBox(height: 10,),
                                    Row(
                                      mainAxisAlignment: MainAxisAlignment.start,
                                      children: [
                                        CustomText(text: cubit!.reviewsModel[index].name,color: AppUI.blackColor,fontSize: 16,fontWeight: FontWeight.w700,),
                                        const SizedBox(width: 5,),
                                        CustomText(text: cubit!.reviewsModel[index].dateCreated!.substring(0,10),),
                                      ],
                                    ),
                                    CustomText(text: cubit!.reviewsModel[index].review,color: AppUI.blackColor,fontSize: 16,),
                                    const Divider(thickness: 1,),
                                  ],
                                );
                              }),
                            ),
                          ],
                        ),
                      );
                    }
                ),
                const SizedBox(height: 10,),
                Row(
                  mainAxisAlignment: MainAxisAlignment.center,
                  children: [
                    Column(
                      children: [
                        CustomText(text: "doYouOwnOrHaveUsedTheProduct".tr(),fontWeight: FontWeight.w600,textAlign: TextAlign.center,),
                        CustomText(text: "tellUsYourOpinionByRating".tr(),textAlign: TextAlign.center,fontSize: 12,),
                        const SizedBox(height: 10,),
                        RatingBar.builder(
                          initialRating: cubit!.rateAdded,
                          minRating: 1,
                          direction: Axis.horizontal,
                          itemPadding: const EdgeInsets.symmetric(horizontal: 4),
                          itemCount: 5,
                          itemSize: 35,
                          unratedColor: AppUI.mainColor.withOpacity(0.1),
                          onRatingUpdate: (rating) {
                            cubit!.rateAdded = rating;
                          },
                          itemBuilder: (BuildContext context, int index) {return const Icon(Icons.star,size: 30,color: Colors.amber,) ; },
                        ),
                        const SizedBox(height: 10,),
                        SizedBox(width: AppUtil.responsiveWidth(context)*0.9,child: CustomInput(controller: cubit!.commentController,hint: "${"writeComment".tr()}...", textInputType: TextInputType.text,maxLines: 3,)),
                        const SizedBox(height: 20,),
                        BlocBuilder<CategoriesCubit,CategoriesState>(
                            buildWhen: (_,state) => state is AddReviewsLoadingState || state is AddReviewsLoadedState || state is AddReviewsErrorState ,
                            builder: (context, state) {
                              if(state is AddReviewsLoadingState){
                                return const LoadingWidget();
                              }
                              return CustomButton(text: "addReview".tr(),width: AppUtil.responsiveWidth(context)*0.9,onPressed: () async {
                                if(cubit!.commentController.text.isEmpty){
                                  AppUtil.errorToast(context, "pleaseAddComment".tr());
                                  return;
                                }
                                await cubit!.addReview(widget.product.id);
                                if(cubit!.addReviewResponse!['id']!=null){
                                  if(!mounted)return;
                                  cubit!.commentController.clear();
                                  cubit!.rateAdded = 5;
                                  AppUtil.successToast(context, "addedSuccessfully".tr());
                                }else{
                                  if(!mounted)return;
                                  AppUtil.errorToast(context, "someThingWrong".tr());
                                }
                              },);
                            }
                        ),
                        const SizedBox(height: 30,),
                        Container(
                          height: 30,color: AppUI.backgroundColor,width: AppUtil.responsiveWidth(context),
                        ),
                        const SizedBox(height: 30,),

                        SizedBox(
                          width: AppUtil.responsiveWidth(context),
                          child: Row(
                            children: [
                              InkWell(
                                onTap: (){
                                  AppUtil.mainNavigator(context, ProductsScreen(catId: product!.categories==null?0:product!.categories[0]['id'], catName: "relatedProducts".tr()));
                                },
                                child: Padding(
                                  padding: const EdgeInsets.symmetric(horizontal: 16),
                                  child: Row(
                                    children: [
                                      CustomText(
                                        text: "relatedProducts".tr(),
                                        fontSize: 18,
                                      ),
                                       SizedBox(width: AppUtil.responsiveWidth(context)*0.48,),
                                      CustomText(text: "seeMore".tr(),color: AppUI.mainColor,)
                                    ],
                                  ),
                                ),
                              ),
                            ],
                          ),
                        ),
                        const SizedBox(height: 10,),
                        BlocBuilder<CategoriesCubit,CategoriesState>(
                          buildWhen: (_,state) => state is RelatedProductsLoadingState || state is RelatedProductsLoadedState || state is RelatedProductsErrorState || state is RelatedProductsEmptyState || state is ChangeFavState,
                          builder: (context, state) {
                            if(state is RelatedProductsLoadingState){
                              return const LoadingWidget();
                            }
                            return Padding(
                              padding: const EdgeInsets.symmetric(horizontal: 16),
                              child: SizedBox(
                                height: 320,
                                width: AppUtil.responsiveWidth(context)*0.92,
                                child: ListView(
                                  shrinkWrap: true,
                                  scrollDirection: Axis.horizontal,
                                  children:
                                  List.generate(cubit!.relatedProducts.length, (index) {
                                    return Row(
                                      children: [
                                        SizedBox(
                                          width: 170,
                                          child: ProductCard(
                                            product: cubit!.relatedProducts[index],
                                            onFav: () {
                                              cubit!.favProduct(cubit!.relatedProducts[index],context);
                                            },
                                          ),
                                        ),
                                        const SizedBox( width: 20,)
                                      ],
                                    );
                                  }),
                                ),
                              ),
                            );
                          }
                        ),
                        const SizedBox(
                          height: 20,
                        ),
                      ],
                    ),
                  ],
                )
              ],
            )
          ],
        ),
      ),
      bottomNavigationBar: BlocBuilder<CategoriesCubit,CategoriesState>(
        buildWhen: (_,state) => state is AddCartErrorState || state is AddCartLoadingState || state is AddCartLoadedState,
        builder: (context, state) {
          final cubit = CategoriesCubit.get(context);
          if(state is AddCartLoadingState){
            return const SizedBox(
              height: 75,
                child: LoadingWidget());
          }
          return CustomCard(
            height: 75,
            child: CustomButton(text: "addToBag".tr(),onPressed: () async {
              // CashHelper.removeSavedString("cartList");
                  List variant = await cubit.getVariationId(cubit.variations,context);
                  if(variant[0] == -1){
                    product!.qty = 1;
                  }
              if(variant[0] != 0){
              for (var element in cubit.variations) {
                if(element.id == variant[0]){
                  element.name = product!.name;
                  element.mainProductId = product!.id.toString();
                  if(!mounted)return;
                  cubit.addToCart(context, element,slugs: variant[1]);
                }
              }
             }else{
                bool exists = await cubit.fetchItemInCart(product!.id);
                if(!exists){
                  product!.qty = 1;
                }
               product!.mainProductId = product!.id.toString();
                if(!mounted)return;
                cubit.addToCart(context, product!);
             }

            },),
          );
        }
      ),
    );
  }

   fetchAttributes() async{
     print('fvnkfjddfjfjn ${product!.id}');
    for (int i=0; i< product!.attributes!.length; i++) {
      optionVisibility.add([]);
      for (var element in product!.attributes![i].options!) {
        if(i==0){
          optionVisibility[i].add(true);
        }else {
          optionVisibility[i].add(false);
        }
      }
    }

    await CategoriesCubit.get(context).fetchProductAttributes(product!);
    if(!mounted)return false;
    await CategoriesCubit.get(context).fetchProductVariations(product!.id);
     if(product!.attributes!.length>=2) {
       for (var element in cubit!.variations) {
         int y = 0;
         for (var element2 in element.attributes!) {
           if(y>0){
           for (int i=0; i< product!.attributes![1].options!.length; i++) {
             if(StringSimilarity.compareTwoStrings(element2.option!.toLowerCase(),product!.attributes![1].options![i].toLowerCase())>0.4 && element2.option!.length >= product!.attributes![1].options![i].length  && cubit!.selectedAttributeIndex[0]['name'] == element.attributes![y-1].option){
              optionVisibility[1][i] = true;
             }
           }
           }
           y++;
         }
       }
     }
     setState(() {});
   }
}
