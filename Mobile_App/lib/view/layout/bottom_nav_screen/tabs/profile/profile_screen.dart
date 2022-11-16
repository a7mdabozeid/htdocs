import 'package:ahshiaka/shared/cash_helper.dart';
import 'package:ahshiaka/shared/components.dart';
import 'package:ahshiaka/utilities/app_ui.dart';
import 'package:ahshiaka/utilities/app_util.dart';
import 'package:ahshiaka/view/auth/auth_screen.dart';
import 'package:ahshiaka/view/layout/bottom_nav_screen/tabs/profile/settings/setting_screen.dart';
import 'package:ahshiaka/view/layout/bottom_nav_screen/tabs/profile/social_accounts/social_accounts_screen.dart';
import 'package:easy_localization/easy_localization.dart';
import 'package:flutter/material.dart';
import 'package:flutter_svg/flutter_svg.dart';

import '../bag/checkout/address/addresses_screen.dart';
import 'change_password/change_password_screen.dart';
import 'edit_profile/edit_profile_screen.dart';
import 'my_orders/my_orders_screen.dart';
class ProfileScreen extends StatefulWidget {
  const ProfileScreen({Key? key}) : super(key: key);

  @override
  _ProfileScreenState createState() => _ProfileScreenState();
}

class _ProfileScreenState extends State<ProfileScreen> {
  String email = "";
  @override
  void didChangeDependencies() {
    // TODO: implement didChangeDependencies
    super.didChangeDependencies();
    getEmail();
  }
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      backgroundColor: AppUI.backgroundColor,
      body: SingleChildScrollView(
        child: Column(
          children: [
            Container(
              padding: const EdgeInsets.all(16),
              color: AppUI.whiteColor,
              child: Stack(
                alignment: AppUtil.rtlDirection(context)?Alignment.topLeft:Alignment.topRight,
                children: [
                  Column(
                    children: [
                      SizedBox(height: MediaQuery.of(context).padding.top,),
                      ListTile(
                        leading: CircleAvatar(
                          backgroundColor: AppUI.mainColor,
                          child: CustomText(text: "ME",color: AppUI.whiteColor,fontSize: 16,fontWeight: FontWeight.w600,),
                        ),
                        title: CustomText(text: "hi".tr(),fontSize: 12,color: AppUI.greyColor,),
                        subtitle: email==""?null:CustomText(text: email),
                      )
                    ],
                  ),
                  Padding(
                    padding: const EdgeInsets.symmetric(vertical: 40),
                    child: InkWell(
                      onTap: (){
                        AppUtil.mainNavigator(context, SettingScreen());
                      },
                      child: CircleAvatar(
                        backgroundColor: AppUI.backgroundColor,
                        radius: 20,
                        child: Icon(Icons.settings_outlined,color: AppUI.blackColor,),
                      ),
                    ),
                  )
                ],
              ),
            ),
            const SizedBox(height: 20,),
            Container(
              color: AppUI.whiteColor,
              padding: const EdgeInsets.all(16),
              child: Column(
                children: [
                  ListTile(
                    onTap: (){
                      if(email!="") {
                        AppUtil.mainNavigator(context, const EditProfileScreen());
                      }else{
                        AppUtil.errorToast(context, "youMustLoginFirst".tr(),type: "login");
                      }
                    },
                    leading: SvgPicture.asset("${AppUI.iconPath}profile.svg"),
                    title: CustomText(text: "profile".tr()),
                    trailing: Icon(Icons.arrow_forward_ios,color: AppUI.blackColor,size: 16,),
                  ),
                  const Divider(),
                  ListTile(
                    onTap: (){
                      if(email == ""){
                        AppUtil.errorToast(context, "youMustLoginFirst".tr(),type: "login");
                      }else {
                        AppUtil.mainNavigator(context, const MyOrdersScreen());
                      }
                    },
                    leading: SvgPicture.asset("${AppUI.iconPath}order.svg"),
                    title: CustomText(text: "myOrders".tr()),
                    trailing: Icon(Icons.arrow_forward_ios,color: AppUI.blackColor,size: 16,),
                  ),
                  const Divider(),
                  ListTile(
                    onTap: (){
                      if(email!="") {
                        AppUtil.mainNavigator(context, const ChangePasswordScreen());
                      }else{
                        AppUtil.errorToast(context, "youMustLoginFirst".tr(),type: "login");
                      }
                    },
                    leading: SvgPicture.asset("${AppUI.iconPath}pass.svg"),
                    title: CustomText(text: "changePass".tr()),
                    trailing: Icon(Icons.arrow_forward_ios,color: AppUI.blackColor,size: 16,),
                  ),
                  const Divider(),
                  ListTile(
                    onTap: (){
                      AppUtil.mainNavigator(context, const AddressesScreen());
                    },
                    leading: SvgPicture.asset("${AppUI.iconPath}location.svg",color: AppUI.blackColor,height: 20,),
                    title: CustomText(text: "addressBook".tr()),
                    trailing: Icon(Icons.arrow_forward_ios,color: AppUI.blackColor,size: 16,),
                  ),
                  // const Divider(),
                  // ListTile(
                  //   leading: SvgPicture.asset("${AppUI.iconPath}credit.svg",color: AppUI.blackColor,),
                  //   title: CustomText(text: "paymentMethod".tr()),
                  //   trailing: Icon(AppUtil.rtlDirection(context)?Icons.arrow_back_ios:Icons.arrow_forward_ios,color: AppUI.blackColor,size: 16,),
                  // ),
                  const Divider(),
                  ListTile(
                    onTap: (){
                      AppUtil.mainNavigator(context, const SocialAccountsScreen());
                    },
                    leading: SvgPicture.asset("${AppUI.iconPath}social.svg",color: AppUI.blackColor,),
                    title: CustomText(text: "socialAccounts".tr()),
                    trailing: Icon(Icons.arrow_forward_ios,color: AppUI.blackColor,size: 16,),
                  ),
                ],
              ),
            ),
            const SizedBox(height: 20,),
            Container(
              color: AppUI.whiteColor,
              padding: const EdgeInsets.all(16),
              child: ListTile(
                onTap: (){
                  if(email==""){
                    AppUtil.mainNavigator(context, const AuthScreen(initialIndex: 1));
                  }else {
                    AppUtil.dialog2(context, "logout".tr(), [
                      CustomText(text: "areYouSureToLogoutFromThisAccount".tr(),
                        color: AppUI.greyColor,
                        textAlign: TextAlign.center,),
                      const SizedBox(height: 20,),
                      Row(
                        children: [
                          CustomButton(text: "submit".tr(),
                            width: AppUtil.responsiveWidth(context) * 0.3,
                            onPressed: () {
                              Navigator.of(context, rootNavigator: true).pop();
                              CashHelper.logOut(context);
                            },),
                          const SizedBox(width: 15,),
                          CustomButton(text: "cancel".tr(),
                            width: AppUtil.responsiveWidth(context) * 0.3,
                            borderColor: AppUI.greyColor,
                            color: AppUI.whiteColor,
                            textColor: AppUI.errorColor,
                            onPressed: () {
                              Navigator.of(context, rootNavigator: true).pop();
                            },),
                        ],
                      )
                    ]);
                  }
                },
                leading: SvgPicture.asset("${AppUI.iconPath}logout.svg",color: AppUI.blackColor,),
                title: CustomText(text: email==""?"signin".tr():"logout".tr()),
                trailing: Icon(Icons.arrow_forward_ios,color: AppUI.blackColor,size: 16,),
              ),
            ),
            const SizedBox(height: 20,),
          ],
        ),
      ),
    );
  }

  getEmail() async {
    email = await CashHelper.getSavedString("user", "");
    setState(() {});
  }
}
