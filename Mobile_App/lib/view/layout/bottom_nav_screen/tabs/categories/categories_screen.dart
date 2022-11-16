import 'package:ahshiaka/view/layout/bottom_nav_screen/tabs/categories/products_screen.dart';
import 'package:easy_localization/easy_localization.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../../../bloc/layout_cubit/categories_cubit/categories_cubit.dart';
import '../../../../../bloc/layout_cubit/categories_cubit/categories_states.dart';
import '../../../../../shared/components.dart';
import '../../../../../utilities/app_ui.dart';
import '../../../../../utilities/app_util.dart';
import '../home/shimmer/home_shimmer.dart';
import 'categories_tab.dart';

class CategoriesScreen extends StatefulWidget {
  const CategoriesScreen({Key? key}) : super(key: key);

  @override
  _CategoriesScreenState createState() => _CategoriesScreenState();
}

class _CategoriesScreenState extends State<CategoriesScreen> {
  List<Widget> tabs = [];

  @override
  Widget build(BuildContext context) {
    return Padding(
      padding: const EdgeInsets.all(16.0),
      child: Column(
        children: [
          SizedBox(height: MediaQuery.of(context).padding.top,),
          SizedBox(
              height: 60,
              child: CustomInput(controller: TextEditingController(),hint: "searchHere".tr(), textInputType: TextInputType.text,borderColor: Color(0xffFAFAFA),fillColor: Color(0xffFAFAFA),prefixIcon: Image.asset("${AppUI.imgPath}search.png"),readOnly: true,onTap: (){
                AppUtil.mainNavigator(context, const ProductsScreen(catId: 0, catName: 'products'));
              },)),
          const SizedBox(height: 20,),
          SizedBox(
            height: AppUtil.responsiveHeight(context)*0.69,
            child:  BlocConsumer<CategoriesCubit,CategoriesState>(
              buildWhen: (context,state){
                return state is CategoriesLoadingState || state is CategoriesEmptyState || state is CategoriesErrorState || state is CategoriesLoadedState;
              },
                listener: (context,state){},
                builder: (context, state) {
                  final cubit = CategoriesCubit.get(context);
                  if(state is CategoriesLoadingState){
                    return const HomeShimmer();
                  }

                  if(state is CategoriesEmptyState){
                    return Center(child: CustomText(text: "noCatAvailable".tr(),fontSize: 24,),);
                  }

                  if(state is CategoriesErrorState){
                    return Center(child: CustomText(text: "error".tr(),fontSize: 24,),);
                  }
                  tabs.clear();
                  for (var element in cubit.categoriesModel) {
                    tabs.add(Tab(child: Text(element.name!,style: TextStyle(color: AppUI.mainColor,fontSize: 16,fontWeight: FontWeight.w600),textAlign: TextAlign.center),),);
                  }
                  return DefaultTabController(
                    length: tabs.length,
                    initialIndex:  cubit.catInitIndex,
                    child: Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        Container(
                          color: AppUI.whiteColor,
                          child: TabBar(
                             onTap: (index){
                               cubit.catInitIndex = index;
                               print(cubit.categoriesModel[index].id);
                                cubit.fetchSubCategories(cubit.categoriesModel[index].id);
                             },
                              indicator: BoxDecoration(borderRadius: BorderRadius.circular(15),color: AppUI.mainColor.withOpacity(0.1)),
                              indicatorPadding: EdgeInsets.symmetric(horizontal: AppUtil.responsiveWidth(context)*0.01),
                              indicatorWeight: 4,
                              indicatorColor: AppUI.mainColor,
                              isScrollable: true,
                              padding: const EdgeInsets.symmetric(horizontal: 10),
                              physics: const BouncingScrollPhysics(),
                              tabs: tabs
                          ),
                        ),

                        Expanded(
                          child:  TabBarView(
                              physics:  const NeverScrollableScrollPhysics(),
                              children: List.generate(tabs.length, (index) {
                                return const CategoriesTab();
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
