import 'package:ahshiaka/utilities/app_util.dart';
import 'package:ahshiaka/view/auth/tabs/sign_in.dart';
import 'package:ahshiaka/view/auth/tabs/sign_up.dart';
import 'package:easy_localization/easy_localization.dart';
import 'package:flutter/gestures.dart';
import 'package:flutter/material.dart';

import '../../shared/components.dart';
import '../../utilities/app_ui.dart';
class AuthScreen extends StatefulWidget {
  final int initialIndex;
  const AuthScreen({Key? key, required this.initialIndex}) : super(key: key);

  @override
  _AuthScreenState createState() => _AuthScreenState();
}

class _AuthScreenState extends State<AuthScreen> with SingleTickerProviderStateMixin{
  var tabBarController;
  @override
  void initState() {
    // TODO: implement initState
    super.initState();
    tabBarController = TabController(length: 2, vsync: this,initialIndex: widget.initialIndex);
  }
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: customAppBar(title: "",toolbarHeight: 0.0,elevation: 0,bottomChildHeight: 100.0,bottomChild: Container(alignment: Alignment.center,
      child: Image.asset("${AppUI.imgPath}logo.png",height: 33,),
        height: 100,
      )),
      body: DefaultTabController(
        length: 2,
        initialIndex:  widget.initialIndex,
        child: Column(
          crossAxisAlignment: CrossAxisAlignment.start,
          children: [
            Container(
              color: AppUI.whiteColor,
              child: Column(
                children: [
                  Container(height: 0.5,width: double.infinity,color: AppUI.shimmerColor,),
                  TabBar(
                      controller: tabBarController,
                      indicatorWeight: 4,
                      indicatorColor: AppUI.mainColor,
                      indicatorPadding: EdgeInsets.symmetric(horizontal: AppUtil.responsiveWidth(context)*0.15),
                      // isScrollable: true,
                      padding: const EdgeInsets.symmetric(horizontal: 10),
                      physics: const BouncingScrollPhysics(),
                      tabs: <Widget>[
                        Tab(child: Text("signup".tr(),style: TextStyle(color: AppUI.mainColor,fontSize: 16,fontWeight: FontWeight.w600),textAlign: TextAlign.center),),
                        Tab(child: Text("signin".tr(),style: TextStyle(color: AppUI.mainColor,fontSize: 16,fontWeight: FontWeight.w600),textAlign: TextAlign.center),),
                      ]
                  ),
                  Container(height: 0.5,width: double.infinity,color: AppUI.shimmerColor,),
                ],
              ),
            ),

             Expanded(
              child:  TabBarView(
                controller: tabBarController,
                  physics:  const NeverScrollableScrollPhysics(),
                  children: const <Widget> [
                    SignUp(),
                    SignIn(),
                  ]),
            ),
          ],
        ),
      ),
    );
  }
}
