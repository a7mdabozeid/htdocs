import 'package:ahshiaka/bloc/layout_cubit/categories_cubit/categories_cubit.dart';
import 'package:ahshiaka/bloc/layout_cubit/categories_cubit/categories_states.dart';
import 'package:ahshiaka/bloc/profile_cubit/profile_cubit.dart';
import 'package:ahshiaka/utilities/app_util.dart';
import 'package:ahshiaka/view/layout/bottom_nav_screen/tabs/categories/products_screen.dart';
import 'package:ahshiaka/view/layout/bottom_nav_screen/tabs/categories/sub_category_screen.dart';
import 'package:cached_network_image/cached_network_image.dart';
import 'package:easy_localization/easy_localization.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:light_carousel/main/light_carousel.dart';

import '../../../../../shared/components.dart';
import '../../../../../utilities/app_ui.dart';
class CategoriesTab extends StatefulWidget {
  const CategoriesTab({Key? key}) : super(key: key);

  @override
  _CategoriesTabState createState() => _CategoriesTabState();
}

class _CategoriesTabState extends State<CategoriesTab> {
  List<Widget> banners = [];


  @override
  Widget build(BuildContext context) {
    final cubit = CategoriesCubit.get(context);

    banners.clear();
    // for (var element in cubit.bannerModel) {
      banners.add(ClipRRect(
        borderRadius: BorderRadius.circular(10),
        child: CachedNetworkImage(imageUrl: "",height: 160,fit: BoxFit.fill,placeholder: (context, url) => Image.asset("${AppUI.imgPath}banner.png",height: 150,fit: BoxFit.fill,),
          errorWidget: (context, url, error) => Image.asset("${AppUI.imgPath}banner.png",height: 170,fit: BoxFit.fill,),),
      ),);
    // }
    return Padding(
      padding: const EdgeInsets.all(8.0),
      child: Column(
        children: [
          SizedBox(
            height: 135,
            child: BlocBuilder<CategoriesCubit,CategoriesState>(
              buildWhen: (context,state) => state is SubCategoriesChangeState,
              builder: (context, state) {
                if(cubit.subCategoriesModel.isEmpty){
                  return Center(child: CustomText(text: "noCatAvailable".tr(),fontSize: 24,),);
                }
                return ListView(
                  scrollDirection: Axis.horizontal,
                  shrinkWrap: true,
                  children: List.generate(cubit.subCategoriesModel.length, (index) {
                    return Row(
                      children: [
                        InkWell(
                          onTap: (){
                            cubit.fetchSubSubCategories(cubit.subCategoriesModel[index].id);
                              if(cubit.subSubCategoriesModel.isEmpty){
                              AppUtil.mainNavigator(context, ProductsScreen(catId: cubit.subCategoriesModel[index].id!, catName: cubit.subCategoriesModel[index].name!,));
                            }else {
                              AppUtil.mainNavigator(context, SubCategoryScreen(catName: cubit.subCategoriesModel[index].name!,));
                            }
                          },
                          child: Column(
                            children: [
                              CircleAvatar(
                                radius: 45,
                                backgroundColor: AppUI.blackColor,
                                child: Padding(
                                  padding: const EdgeInsets.all(1.0),
                                  child: CircleAvatar(
                                    radius: 45,
                                    backgroundColor: AppUI.whiteColor,
                                    child: Padding(
                                      padding: const EdgeInsets.all(2.0),
                                      child: ClipRRect(
                                        borderRadius: BorderRadius.circular(50),
                                        child: CachedNetworkImage(imageUrl: cubit.subCategoriesModel[index].image==null?"":cubit.subCategoriesModel[index].image!.src!,placeholder: (context, url) => Image.asset("${AppUI.imgPath}story.png",height: 80,width: 80,fit: BoxFit.fill,),
                                          errorWidget: (context, url, error) => Image.asset("${AppUI.imgPath}story.png",height: 80,width: 80,fit: BoxFit.fill,),),
                                      ),
                                    ),
                                  ),
                                ),
                              ),
                              const SizedBox(height: 5,),
                              CustomText(text: cubit.subCategoriesModel[index].name)
                            ],
                          ),
                        ),
                        const SizedBox(width: 5,)
                      ],
                    );
                  }),
                );
              }
            ),
          ),
          const SizedBox(height: 25,),
          SizedBox(
              height: 200.0,
              width: double.infinity,
              child: LightCarousel(
                images: [
                  ClipRRect(
                    borderRadius: BorderRadius.circular(10),
                    child: CachedNetworkImage(imageUrl: cubit.categoriesModel[cubit.catInitIndex].links!.collection![0].href!,height: 160,fit: BoxFit.fill,placeholder: (context, url) => Image.asset("${AppUI.imgPath}thope.png",height: 150,fit: BoxFit.fill,),
                      errorWidget: (context, url, error) => Image.asset("${AppUI.imgPath}thope.png",height: 170,fit: BoxFit.fill,),),
                  ),
                ],
                dotSize: 5.0,
                dotSpacing: 15.0,
                dotColor: AppUI.whiteColor,
                dotIncreasedColor: Colors.transparent,
                indicatorBgPadding: 20.0,
                dotBgColor: Colors.purple.withOpacity(0.0),
                borderRadius: true,
              )
          ),
        ],
      ),
    );
  }
}
