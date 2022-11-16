import 'package:ahshiaka/shared/components.dart';
import 'package:ahshiaka/utilities/app_ui.dart';
import 'package:ahshiaka/utilities/app_util.dart';
import 'package:ahshiaka/view/layout/bottom_nav_screen/tabs/categories/products_screen.dart';
import 'package:ahshiaka/view/layout/bottom_nav_screen/tabs/home/shimmer/home_shimmer.dart';
import 'package:easy_localization/easy_localization.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import 'package:google_fonts/google_fonts.dart';

import '../../../../../bloc/layout_cubit/categories_cubit/categories_cubit.dart';
import '../../../../../bloc/layout_cubit/categories_cubit/categories_states.dart';
import 'home_tab.dart';
class HomeScreen extends StatefulWidget {
  const HomeScreen({Key? key}) : super(key: key);

  @override
  _HomeScreenState createState() => _HomeScreenState();
}

class _HomeScreenState extends State<HomeScreen> with SingleTickerProviderStateMixin{
  List<Widget> tabs = [];

  @override
  Widget build(BuildContext context) {
    final cubit = CategoriesCubit.get(context);

    return Padding(
      padding: const EdgeInsets.all(2.0),
      child: Column(
        children: [
          Padding(
            padding: const EdgeInsets.symmetric(horizontal: 16),
            child: SizedBox(
              height: 60,
                child: CustomInput(controller: TextEditingController(),hint: "searchHere".tr(), textInputType: TextInputType.text,borderColor: Color(0xffFAFAFA),fillColor: Color(0xffFAFAFA),prefixIcon: Image.asset("${AppUI.imgPath}search.png"),readOnly: true,onTap: (){
                  AppUtil.mainNavigator(context, const ProductsScreen(catId: 0, catName: 'products'));
                },)),
          ),
          const SizedBox(height: 20,),
          Expanded(
            child: BlocConsumer<CategoriesCubit,CategoriesState>(
                buildWhen: (context,state){
                  return state is CategoriesLoadingState || state is CategoriesEmptyState || state is CategoriesErrorState || state is CategoriesLoadedState;
                },
              listener: (context,state){},
              builder: (context, state) {
                if(state is CategoriesLoadingState){
                  return const HomeShimmer();
                }

                if(state is CategoriesEmptyState){
                  return Center(child: CustomText(text: "noProductsAvailable".tr(),fontSize: 24,),);
                }

                if(state is CategoriesErrorState){
                  return Center(child: CustomText(text: "error".tr(),fontSize: 24,),);
                }
                cubit.tapBarController ??= TabController(length: cubit.categoriesModel.length, vsync: this);

                tabs.clear();
                for (var element in cubit.categoriesModel) {
                  tabs.add(Tab(child: Text("${element.name!}",style: TextStyle(color: AppUI.mainColor,fontSize: 14,fontWeight: FontWeight.w600),textAlign: TextAlign.center),),);
                }
                return DefaultTabController(
                  length: tabs.length,
                  initialIndex:  cubit.initialIndex,
                  child: Column(
                    crossAxisAlignment: CrossAxisAlignment.start,
                    children: [
                      Container(
                        color: AppUI.whiteColor,
                        child: TabBar(
                          controller: cubit.tapBarController,
                          onTap: (index){
                            AppUtil.mainNavigator(context, ProductsScreen(catId: cubit.categoriesModel[index].id!, catName: cubit.categoriesModel[index].name!));
                            // cubit.tapBarController!.index = 0;
                            // cubit.tapBarController!.index = cubit.tapBarController!.previousIndex;
                            // cubit.initialIndex = index;
                            // cubit.fetchNewArrivalProductsByCategory(catId: cubit.categoriesModel[index].id, page: 1, perPage: 20,ratingCount: 1,);
                          },
                          // indicator: BoxDecoration(borderRadius: BorderRadius.circular(15),color: AppUI.mainColor,),
                            indicatorPadding: const EdgeInsets.symmetric(horizontal: 5),
                            indicatorWeight: 4,
                            indicatorColor: AppUI.mainColor,
                            isScrollable: true,
                            indicatorSize: TabBarIndicatorSize.label,
                            padding: const EdgeInsets.symmetric(horizontal: 10),
                            physics: const BouncingScrollPhysics(),
                            tabs: tabs
                        ),
                      ),
                      Expanded(
                        child:  TabBarView(
                            physics:  const NeverScrollableScrollPhysics(),
                            children: List.generate(tabs.length, (index) {
                              return HomeTab(catId: cubit.categoriesModel[index].id);
                            })),
                      ),
                    ],
                  ),
                );

              }
            ),
          ),
        ],
      ),
    );

  }
}
