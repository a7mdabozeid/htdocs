import 'dart:convert';

import 'package:ahshiaka/bloc/layout_cubit/categories_cubit/categories_cubit.dart';
import 'package:ahshiaka/models/categories/products_model.dart';
import 'package:ahshiaka/shared/components.dart';
import 'package:ahshiaka/utilities/app_ui.dart';
import 'package:easy_localization/easy_localization.dart';
import 'package:flutter/material.dart';
class Details extends StatelessWidget {
  final ProductModel product;
  const Details({Key? key, required this.product}) : super(key: key);

  @override
  Widget build(BuildContext context) {
    final cubit = CategoriesCubit.get(context);
    return Padding(
      padding: const EdgeInsets.all(16.0),
      child: Column(
        children: [
          if(cubit.variations.isNotEmpty)
          CustomText(text: cubit.variations[0].description==""?"noDetailsAvailableToThisProduct".tr():htmlEscape.convert(cubit.variations[0].description==""?"noDetailsAvailableToThisProduct".tr():cubit.variations[0].description!))
          else
          CustomText(text: "noDetailsAvailableToThisProduct".tr())
          // Row(
          //   children: [
          //     Expanded(
          //       child: Row(
          //         children: [
          //           Image.asset("${AppUI.imgPath}check.png",height: 40,),
          //           const SizedBox(width: 10,),
          //           Expanded(child: CustomText(text: "100% ${"genuine".tr()}".tr()))
          //         ],
          //       ),
          //     ),
          //     Expanded(
          //       child: Row(
          //         children: [
          //           Image.asset("${AppUI.imgPath}money.png",height: 40,),
          //           const SizedBox(width: 10,),
          //           Expanded(child: CustomText(text: "cashOnDelivery".tr()))
          //         ],
          //       ),
          //     ),
          //   ],
          // ),
          // const SizedBox(height: 20,),
          // Row(
          //   children: [
          //     Expanded(
          //       child: Row(
          //         children: [
          //           Image.asset("${AppUI.imgPath}extchange.png",height: 40,),
          //           const SizedBox(width: 10,),
          //           Expanded(child: CustomText(text: "exchange".tr()))
          //         ],
          //       ),
          //     ),
          //     Expanded(
          //       child: Row(
          //         children: [
          //           Image.asset("${AppUI.imgPath}delivery.png",height: 40,),
          //           const SizedBox(width: 10,),
          //           Expanded(child: CustomText(text: "fastDelivery".tr()))
          //         ],
          //       ),
          //     ),
          //   ],
          // ),
          // // const SizedBox(height: 40,),
          // // Row(
          // //   children: [
          // //     CircleAvatar(
          // //       radius: 5,
          // //       backgroundColor: AppUI.backgroundColor,
          // //     ),
          // //     const SizedBox(width: 10,),
          // //     CustomText(text: "Comfortable and soft polyester fabric"),
          // //   ],
          // // ),
          // // Row(
          // //   children: [
          // //     CircleAvatar(
          // //       radius: 5,
          // //       backgroundColor: AppUI.backgroundColor,
          // //     ),
          // //     const SizedBox(width: 10,),
          // //     CustomText(text: "Comfortable and soft polyester fabric"),
          // //   ],
          // // ),
          // // Row(
          // //   children: [
          // //     CircleAvatar(
          // //       radius: 5,
          // //       backgroundColor: AppUI.backgroundColor,
          // //     ),
          // //     const SizedBox(width: 10,),
          // //     CustomText(text: "Comfortable and soft polyester fabric"),
          // //   ],
          // // ),

        ],
      ),
    );
  }
}
