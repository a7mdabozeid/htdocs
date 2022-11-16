import 'package:ahshiaka/view/layout/bottom_nav_screen/tabs/bag/bag_screen.dart';
import 'package:ahshiaka/view/layout/bottom_nav_screen/tabs/categories/categories_screen.dart';
import 'package:ahshiaka/view/layout/bottom_nav_screen/tabs/home/home_screen.dart';
import 'package:ahshiaka/view/layout/bottom_nav_screen/tabs/profile/profile_screen.dart';
import 'package:ahshiaka/view/layout/bottom_nav_screen/tabs/wish_list_screen.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../bloc/layout_cubit/bottom_nav_cubit.dart';
import '../../../bloc/layout_cubit/bottom_nav_states.dart';
import '../../../shared/components.dart';
import '../../../utilities/app_ui.dart';
import '../../../utilities/app_util.dart';
import 'bottom_nav_widget.dart';

class BottomNavTabsScreen extends StatefulWidget {
  const BottomNavTabsScreen({Key? key}) : super(key: key);

  @override
  _BottomNavTabsScreenState createState() => _BottomNavTabsScreenState();
}

class _BottomNavTabsScreenState extends State<BottomNavTabsScreen> {
  @override
  void initState() {
    // TODO: implement initState
    super.initState();
    AppUtil.showPushNotification(context);

  }
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      resizeToAvoidBottomInset: false,
      appBar: context.watch<BottomNavCubit>().currentIndex==0?
      customAppBar(title: Row(
        mainAxisAlignment: MainAxisAlignment.center,
        children: [
          Image.asset("${AppUI.imgPath}logo.png",width: 120,),
        ],
      ),backgroundColor: AppUI.whiteColor,elevation: 0):null,
      body: BottomNavCubit.get(context).currentIndex==0?const HomeScreen():BottomNavCubit.get(context).currentIndex==1?const CategoriesScreen():BottomNavCubit.get(context).currentIndex==2?const BagScreen():BottomNavCubit.get(context).currentIndex==3?const WishListScreen():ProfileScreen(),
      bottomNavigationBar: BlocConsumer<BottomNavCubit,BottomNavState>(
        listener: (context,index){

        },
        builder: (context, state,) {
          var bottomNavProvider = BottomNavCubit.get(context);
          return BottomNavBar(currentIndex: bottomNavProvider.currentIndex,
            onTap0: (){
            bottomNavProvider.setCurrentIndex(0);
          },
            onTap1: (){
              bottomNavProvider.setCurrentIndex(1);
            },
            onTap2: (){
              bottomNavProvider.setCurrentIndex(2);
            },

            onTap3: (){
              bottomNavProvider.setCurrentIndex(3);
            },

            onTap4: (){
              bottomNavProvider.setCurrentIndex(4);
            },

          );
        }
      ),
    );
  }
}
