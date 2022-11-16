import 'package:ahshiaka/models/auth_models/error_user_model.dart';
import 'package:ahshiaka/shared/components.dart';
import 'package:easy_localization/easy_localization.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';

import '../../../bloc/auth_cubit/auth_cubit.dart';
import '../../../bloc/auth_cubit/auth_states.dart';
import '../../../utilities/app_ui.dart';
import '../../../utilities/app_util.dart';
import '../../layout/bottom_nav_screen/bottom_nav_tabs_screen.dart';
class SignUp extends StatefulWidget {
  const SignUp({Key? key}) : super(key: key);

  @override
  _SignUpState createState() => _SignUpState();
}

class _SignUpState extends State<SignUp> {
  @override
  Widget build(BuildContext context) {
    return BlocConsumer<AuthCubit,AuthState>(
        listener: (context,state){},
        builder: (context, state) {
          var cubit = AuthCubit.get(context);
          return Form(
            key: cubit.registerFormKey,
            child: Padding(
              padding: const EdgeInsets.all(16.0),
              child: Column(
                crossAxisAlignment: CrossAxisAlignment.start,
                children: [
                  SizedBox(
                    height: AppUtil.responsiveHeight(context)-230,
                    child: SingleChildScrollView(
                      child: Column(
                        crossAxisAlignment: CrossAxisAlignment.start,
                        children: [
                          CustomText(text: "createAccount".tr(),fontSize: 20.0,fontWeight: FontWeight.w600,),
                          const SizedBox(height: 25,),
                          CustomInput(controller: cubit.registerPhone, hint: "phoneNumber".tr(), textInputType: TextInputType.phone,suffixIcon: Image.asset("${AppUI.imgPath}sar.png",width: 50,),),
                          const SizedBox(height: 25,),
                          CustomInput(controller: cubit.registerFirstName, hint: "firstName".tr(), textInputType: TextInputType.text),
                          const SizedBox(height: 25,),
                          CustomInput(controller: cubit.registerLastName, hint: "lastName".tr(), textInputType: TextInputType.text),
                          const SizedBox(height: 25,),
                          CustomInput(controller: cubit.registerUserName, hint: "userName".tr(), textInputType: TextInputType.text),
                          const SizedBox(height: 25,),
                          CustomInput(controller: cubit.registerEmail, hint: "email".tr(), textInputType: TextInputType.emailAddress,),
                          const SizedBox(height: 25,),
                          CustomInput(controller: cubit.registerPassword, hint: "pass".tr(), textInputType: TextInputType.text,obscureText: cubit.registerVisibility,suffixIcon: IconButton(onPressed: (){
                            cubit.registerChangeVisibility();
                          }, icon: Icon(cubit.registerVisibilityIcon,color: AppUI.iconColor,size: 25,)),),
                          const SizedBox(height: 25,),
                          CustomInput(controller: cubit.registerConfirmPassword, hint: "rePass".tr(), textInputType: TextInputType.text,obscureText: cubit.registerConfirmVisibility,suffixIcon: IconButton(onPressed: (){
                            cubit.registerConfirmChangeVisibility();
                          }, icon: Icon(cubit.registerConfirmVisibilityIcon,color: AppUI.iconColor,size: 25,)),),

                          const SizedBox(height: 30,),
                          if(state is RegisterLoadingState)
                            const LoadingWidget()
                          else
                            CustomButton(text: "signup".tr(),width: double.infinity,onPressed: () async {
                              if(cubit.registerPhone.text.length<9 && cubit.registerPhone.text.length>10){
                                AppUtil.errorToast(context, "inValidPhone".tr());
                                return ;
                              }
                              if(!AppUtil.isEmailValidate(cubit.registerEmail.text)){
                                AppUtil.errorToast(context, "inValidEmail".tr());
                                return ;
                              }

                              if(cubit.registerFormKey.currentState!.validate()) {
                                await cubit.register(context);
                                if(cubit.registerModel! is !ErrorUserModel ){
                                  if(!mounted)return;
                                  AppUtil.successToast(context, "doneCreatedUser".tr());
                                  // AppUtil.removeUntilNavigator(context, const BottomNavTabsScreen());
                                }else{
                                  if(!mounted)return;
                                  AppUtil.errorToast(context, "loginFailed".tr());
                                }
                                // AppUtil.mainNavigator(context, const VerificationScreen(type:"register"));
                              }
                            },),
                          const SizedBox(height: 20,),
                          Row(
                            children: [
                              const Expanded(child: Divider()),
                              Expanded(child: CustomText(text: "orLoginWith".tr(),color: AppUI.shimmerColor,textAlign: TextAlign.center,)),
                              const Expanded(child: Divider()),
                            ],
                          ),
                          const SizedBox(height: 20,),
                          Row(
                            mainAxisAlignment: MainAxisAlignment.center,
                            children: [
                              Image.asset("${AppUI.imgPath}google.png",width: 80,),
                              const SizedBox(width: 20,),
                              Image.asset("${AppUI.imgPath}facebook.png",width: 80,),
                              const SizedBox(width: 20,),
                              Image.asset("${AppUI.imgPath}apple.png",width: 80,),
                            ],
                          )
                        ],
                      ),
                    ),
                  ),
                ],
              ),
            ),
          );
        }
    );
  }
}
