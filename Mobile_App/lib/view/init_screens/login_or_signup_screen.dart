import 'package:ahshiaka/shared/components.dart';
import 'package:ahshiaka/utilities/app_ui.dart';
import 'package:ahshiaka/utilities/app_util.dart';
import 'package:easy_localization/easy_localization.dart';
import 'package:flutter/material.dart';

import '../auth/auth_screen.dart';
import '../layout/bottom_nav_screen/bottom_nav_tabs_screen.dart';
class LoginOrSignupScreen extends StatefulWidget {
  const LoginOrSignupScreen({Key? key}) : super(key: key);

  @override
  _LoginOrSignupScreenState createState() => _LoginOrSignupScreenState();
}

class _LoginOrSignupScreenState extends State<LoginOrSignupScreen> {
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      body: Column(
        children:  [
          Expanded(flex: 3,child: Image.asset("${AppUI.imgPath}group.png",width: double.infinity,fit: BoxFit.fill,)),
          Expanded(
            flex: 2,
            child: Row(
              mainAxisAlignment: MainAxisAlignment.center,
              children: [
                Padding(
                  padding: const EdgeInsets.all(16.0),
                  child: Column(
                    mainAxisAlignment: MainAxisAlignment.center,
                    children: [
                      CustomText(text: "welcomeToAlshiaka".tr(),fontWeight: FontWeight.w700,fontSize: 20,),
                      CustomText(text: "luxuryShoppingWithoutLimits".tr(),fontSize: 12,color: AppUI.shimmerColor,),
                      const SizedBox(height: 15,),
                      CustomButton(text: "signup".tr(),onPressed: (){
                        AppUtil.mainNavigator(context, AuthScreen(initialIndex: 0));
                      },),
                      const SizedBox(height: 10,),
                      CustomButton(text: "signin".tr(),borderColor: AppUI.mainColor,color: AppUI.whiteColor,textColor: AppUI.mainColor,onPressed: (){
                        AppUtil.mainNavigator(context, AuthScreen(initialIndex: 1));
                      },),
                      const SizedBox(height: 12,),
                      InkWell(
                        onTap: (){
                          AppUtil.removeUntilNavigator(
                              context, const BottomNavTabsScreen());
                        },
                          child: CustomText(text: "continueAsGuest".tr(),)),
                    ],
                  ),
                ),
              ],
            ),
          )
        ],
      ),
    );
  }
}
