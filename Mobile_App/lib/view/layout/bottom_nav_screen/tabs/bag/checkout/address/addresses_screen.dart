import 'package:ahshiaka/bloc/layout_cubit/checkout_cubit/checkout_cubit.dart';
import 'package:ahshiaka/bloc/layout_cubit/checkout_cubit/checkout_state.dart';
import 'package:easy_localization/easy_localization.dart';
import 'package:flutter/material.dart';
import 'package:flutter_bloc/flutter_bloc.dart';
import '../../../../../../../../shared/components.dart';
import '../../../../../../../../utilities/app_ui.dart';
import '../../../../../../../../utilities/app_util.dart';
import '../../../../../../../models/checkout/addresses_model.dart';
import '../../../../../../../shared/cash_helper.dart';
import 'add_new_address.dart';
class AddressesScreen extends StatefulWidget {
  const AddressesScreen({Key? key}) : super(key: key);

  @override
  _AddressesScreenState createState() => _AddressesScreenState();
}

class _AddressesScreenState extends State<AddressesScreen> {

  @override
  Widget build(BuildContext context) {
    final cubit = CheckoutCubit.get(context);
    return Scaffold(
      backgroundColor: AppUI.backgroundColor,
      body: Column(
        children: [
          CustomAppBar(title: "addresses".tr(), leading: GestureDetector(
              onTap: (){
                AppUtil.mainNavigator(context, const AddNewAddress());
              },
              child: Icon(Icons.add,color: AppUI.blackColor,size: 25,)),),
          BlocBuilder<CheckoutCubit,CheckoutState>(
            buildWhen: (_,state) => state is AddressesState,
            builder: (context, state) {
              if(cubit.addresses!.shipping==null){
                return Center(child: CustomText(text: "noAddressesFound".tr(),fontSize: 18,));
              }
              return Expanded(
                child: ListView(
                  shrinkWrap: true,
                  children: List.generate(cubit.addresses!.shipping!.address0!.length, (index) {
                    return Column(
                      crossAxisAlignment: CrossAxisAlignment.start,
                      children: [
                        InkWell(
                          onTap: (){
                            cubit.selectedAddress = AddressesModel(fullName: cubit.addresses!.shipping!.address0![index].shippingFirstName
                                ,surName: cubit.addresses!.shipping!.address0![index].shippingLastName
                                ,phoneNumber: cubit.addresses!.shipping!.address0![index].shippingPhone
                                ,email: cubit.addresses!.shipping!.address0![index].shippingEmail
                                ,address: cubit.addresses!.shipping!.address0![index].shippingAddress1
                                ,state: cubit.addresses!.shipping!.address0![index].shippingState
                                ,address2: cubit.addresses!.shipping!.address0![index].shippingAddress2
                                ,city: cubit.addresses!.shipping!.address0![index].shippingCity
                                ,postCode: cubit.addresses!.shipping!.address0![index].shippingPostcode
                                ,country: cubit.addresses!.shipping!.address0![index].shippingCountry
                                , defaultAddress: index==0?true:false);

                            cubit.emit(AddressesState());
                            Navigator.pop(context);
                          },
                          child: Container(
                            color: AppUI.whiteColor,
                            width: double.infinity,
                            padding: const EdgeInsets.all(16),
                            child: Row(
                              children: [
                                Expanded(
                                  flex: 3,
                                  child: Column(
                                    crossAxisAlignment: CrossAxisAlignment.start,
                                    children: [
                                      CustomText(text: index == 0?"Primary Address":"",color: AppUI.mainColor,fontSize: 12,),
                                      const SizedBox(height: 6,),
                                      CustomText(text: cubit.addresses!.shipping!.address0![index].shippingCity,color: AppUI.blackColor,fontSize: 12,),
                                      const SizedBox(height: 6,),
                                      CustomText(text: cubit.addresses!.shipping!.address0![index].shippingAddress1,fontSize: 12,fontWeight: FontWeight.w100,),
                                    ],
                                  ),
                                ),
                                Expanded(child: Row(
                                  mainAxisAlignment: MainAxisAlignment.end,
                                  children: [
                                    InkWell(
                                      onTap: (){
                                            AppUtil.mainNavigator(context, AddNewAddress(address: cubit.addresses!.shipping!.address0![index],addressKey: cubit.addresses!.shipping!.addressesKey![index]));
                                      },
                                        child: Icon(Icons.edit,color: AppUI.greyColor,size: 19,)),
                                    const SizedBox(width: 15,),
                                    InkWell(
                                      onTap: () async {
                                        AppUtil.dialog2(context, "", [
                                          const LoadingWidget(),
                                          const SizedBox(height: 30,)
                                        ]);
                                        await cubit.deleteAddress("address_$index");
                                        Navigator.of(context,rootNavigator: true).pop();
                                      },
                                        child: Icon(Icons.delete,color: AppUI.greyColor,size: 19,)),
                                  ],
                                )),
                              ],
                            ),
                          ),
                        ),
                        const SizedBox(height: 15,)
                      ],
                    );
                  }),
                ),
              );
            }
          ),
        ],
      ),
    );
  }

}
