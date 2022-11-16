import 'package:ahshiaka/bloc/layout_cubit/checkout_cubit/checkout_cubit.dart';
import 'package:ahshiaka/models/checkout/shipping_model.dart';
import 'package:ahshiaka/view/layout/bottom_nav_screen/tabs/bag/checkout/address/select_address_from_map.dart';
import 'package:easy_localization/easy_localization.dart';
import 'package:flutter/material.dart';
import 'package:flutter_svg/svg.dart';
import '../../../../../../../../shared/components.dart';
import '../../../../../../../../utilities/app_ui.dart';
import '../../../../../../../../utilities/app_util.dart';
import '../../../../../../../shared/cash_helper.dart';

class AddNewAddress extends StatefulWidget {
  final Address0? address;
  final String? addressKey;
  const AddNewAddress({Key? key, this.address, this.addressKey}) : super(key: key);

  @override
  _AddNewAddressState createState() => _AddNewAddressState();
}

class _AddNewAddressState extends State<AddNewAddress> {
  late List address;
  String user="",email="",selectedRegion = "",selectedCity = "";
  int selectedRegionIndex=0;
  List <String> regions = [],regionsAr = [];
  List<List<String>> cities = [],citiesAr = [];
  @override
  void initState() {
    // TODO: implement initState
    super.initState();
    getData();
  }
  @override
  Widget build(BuildContext context) {
    final cubit = CheckoutCubit.get(context);

    cubit.nameController.text = user;
    cubit.surNameController.text = user;
    cubit.emailController.text = email;
    cubit.stateController.text = "Saudi Arabia";

    return Scaffold(
      backgroundColor: AppUI.backgroundColor,
      body: Form(
        key: cubit.newAddressFormKey,
        child: Column(
          children: [
            CustomAppBar(title: "addresses".tr(),
                leading: InkWell(
                onTap: () async {
                  address = await AppUtil.mainNavigator(context, const SelectAddressFromMap());
                  cubit.addressController.text = address[1];
                  cubit.postCodeController.text = address[0];
                  // cubit.stateController.text = address[1].split(',')[0];
                  // cubit.countryController.text = address[1].split(',')[1];
                  // cubit.cityController.text = address[1].split(',')[2];
                },
                child: SvgPicture.asset("${AppUI.iconPath}location.svg",color: AppUI.blackColor,))
            ),
            SizedBox(
                  height: AppUtil.responsiveHeight(context)*0.86,
                  child: SingleChildScrollView(
                    child: Column(
                      children: [
                        Padding(
                          padding: const EdgeInsets.all(24.0),
                          child: Row(
                            children: [
                              CustomText(text: "accountInformation".tr(),fontWeight: FontWeight.w500,),
                            ],
                          ),
                        ),
                        Container(
                          color: AppUI.whiteColor,
                          padding: const EdgeInsets.all(16),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              CustomText(text: "fullName".tr(),color: AppUI.greyColor,),
                              CustomInput(controller: cubit.nameController , textInputType: TextInputType.text,),
                              const SizedBox(height: 10,),
                              CustomText(text: "surname".tr(),color: AppUI.greyColor,),
                              CustomInput(controller: cubit.surNameController, textInputType: TextInputType.text,),
                              const SizedBox(height: 10,),
                              CustomText(text: "phoneNumber".tr(),color: AppUI.greyColor,),
                              CustomInput(controller: cubit.phoneController, textInputType: TextInputType.phone,),
                            ],
                          ),
                        ),
                        Padding(
                          padding: const EdgeInsets.all(24.0),
                          child: Row(
                            children: [
                              CustomText(text: "addressInformation".tr(),fontWeight: FontWeight.w500,),
                            ],
                          ),
                        ),
                        Container(
                          color: AppUI.whiteColor,
                          padding: const EdgeInsets.all(16),
                          child: Column(
                            crossAxisAlignment: CrossAxisAlignment.start,
                            children: [
                              CustomText(text: "email".tr(),color: AppUI.greyColor,),
                              CustomInput(controller: cubit.emailController, textInputType: TextInputType.text,),
                              const SizedBox(height: 10,),
                              CustomText(text: "state".tr(),color: AppUI.greyColor,),
                              CustomInput(controller: cubit.stateController, textInputType: TextInputType.text,readOnly: true,),
                              const SizedBox(height: 10,),
                              const SizedBox(height: 10,),
                              CustomText(text: "region".tr(),color: AppUI.greyColor,),
                              CustomInput(controller: cubit.countryController, textInputType: TextInputType.text,suffixIcon: const Icon(Icons.keyboard_arrow_down),readOnly: true,onTap: (){
                                AppUtil.dialog2(context, "region".tr(), List.generate(regions.length, (index) {
                                  return Column(
                                    children: [
                                      InkWell(
                                        onTap: (){
                                          Navigator.of(context,rootNavigator: true).pop();
                                          cubit.countryController.text = AppUtil.rtlDirection(context)?regionsAr[index]:regions[index];
                                          selectedRegion = regions[index];
                                          selectedRegionIndex = index;
                                        },
                                          child: Row(
                                            children: [
                                              CustomText(text: AppUtil.rtlDirection(context)?regionsAr[index]:regions[index]),
                                            ],
                                          )),
                                      const Divider(),
                                    ],
                                  );
                                }));
                              },),
                              CustomText(text: "city".tr(),color: AppUI.greyColor,),
                              CustomInput(controller: cubit.cityController, textInputType: TextInputType.text,suffixIcon: const Icon(Icons.keyboard_arrow_down),readOnly: true,onTap: (){
                                AppUtil.dialog2(context, "city".tr(), [
                                  SizedBox(
                                    height: AppUtil.responsiveHeight(context)*0.7,
                                    child: SingleChildScrollView(
                                      child: Column(
                                        children: List.generate(cities[selectedRegionIndex].length, (index) {
                                          return Column(
                                            children: [
                                              InkWell(
                                                  onTap: (){
                                                    Navigator.of(context,rootNavigator: true).pop();
                                                    cubit.cityController.text = AppUtil.rtlDirection(context)?citiesAr[selectedRegionIndex][index]:cities[selectedRegionIndex][index];
                                                    selectedCity = cities[selectedRegionIndex][index];
                                                  },
                                                  child: Row(
                                                    children: [
                                                      CustomText(text: AppUtil.rtlDirection(context)?citiesAr[selectedRegionIndex][index]:cities[selectedRegionIndex][index]),
                                                    ],
                                                  )),
                                              const Divider(),
                                            ],
                                          );
                                        }),
                                      ),
                                    ),
                                  )
                                ]
                                );
                              },),
                              const SizedBox(height: 10,),
                              CustomText(text: "address".tr(),color: AppUI.greyColor,),
                              CustomInput(controller: cubit.addressController, textInputType: TextInputType.text,),
                              const SizedBox(height: 10,),
                              // CustomText(text: "${"address".tr()} 2",color: AppUI.greyColor,),
                              // CustomInput(controller: cubit.address2Controller, textInputType: TextInputType.text,),
                              const SizedBox(height: 10,),
                              CustomText(text: "postCode".tr(),color: AppUI.greyColor,),
                              CustomInput(controller: cubit.postCodeController, textInputType: TextInputType.text,),
                              const SizedBox(height: 10,),
                              // CustomText(text: "country".tr(),color: AppUI.greyColor,),
                              // CustomInput(controller: cubit.countryController, textInputType: TextInputType.text,),
                            ],
                          ),
                        ),
                        // BlocBuilder<CheckoutCubit,CheckoutState>(
                        //     buildWhen: (context,state) => state is AddressesState,
                        //     builder: (context, state) {
                        //       return Padding(
                        //         padding: const EdgeInsets.all(24.0),
                        //         child: InkWell(
                        //           onTap: (){
                        //             cubit.changeDefaultState();
                        //           },
                        //           child: Row(
                        //             children: [
                        //               CustomCard(
                        //                 padding: 0.0,radius: 10,width: 35,height: 35,elevation: 0,
                        //                 color: cubit.defaultAddress?AppUI.orangeColor:AppUI.whiteColor,
                        //                 border: AppUI.orangeColor,
                        //                 child: Icon(Icons.check,color: AppUI.whiteColor,),
                        //               ),
                        //               const SizedBox(width: 7,),
                        //               CustomText(text: "setAsMyDefaultAddress".tr())
                        //             ],
                        //           ),
                        //         ),
                        //       );
                        //     }
                        // ),
                        InkWell(
                          onTap: () async {
                            if(cubit.newAddressFormKey.currentState!.validate()) {
                              print(widget.addressKey);
                              if(!AppUtil.isEmailValidate(cubit.emailController.text)){
                                AppUtil.errorToast(context, "inValidEmail".tr());
                                return;
                              }
                              AppUtil.dialog2(context, "", [
                                const LoadingWidget(),
                                const SizedBox(height: 30,),
                              ]);
                              await cubit.saveAddress(context,address_id: widget.addressKey);
                            }
                          },
                          child: CustomButton(text: "save".tr()),
                        ),
                        const SizedBox(height: 30,)
                      ],
                    ),
                  ),
                )

          ],
        ),
      ),
    );
  }
  getData() async {
    final cubit = CheckoutCubit.get(context);

    user = await CashHelper.getSavedString("user", "");
    email = await CashHelper.getSavedString("email", "");

    regions = [
      "Al Baha Area","Al Madinah Area","Al Qassim Area","Aljouf Area","Aser Area","Gizan Area","Hail Area","Makkah Area","Najran Area","Riyadh Area","Tabuk Area","The Eastern Area","The Northern border Area"
    ];
    regionsAr = [
      "منطقة الباحة","منطقة المدينة المنورة","منطقة القصيم","منطقة الجوف","منطقة عسير","منطقة جيزان","منطقة حائل","منطقة مكة المكرمة","منطقة نجران","منطقة الرياض","منطقة تبوك","منطقة الشرقية","منطقة الحدود الشمالية"
    ];
    cities = [
      [
        "Aqiq",
        "Atawleh",
        "Baha",
        "BilJurashi",
        "Gilwa",
        "Hajrah",
        "Mandak",
        "Mikhwa",
        "Subheka"
      ],
      [
        "Al Ais",
        "Bader",
        "Hinakeya",
        "Khaibar",
        "Madinah",
        "Mahad Al Dahab",
        "Oula",
        "Yanbu",
        "Yanbu Al Baher",
        "Yanbu Nakhil"
      ],
      [
        "Aba Alworood",
        "Al Batra",
        "Al Dalemya",
        "Al Fuwaileq / Ar Rishawiyah",
        "Al Khishaybi",
        "Al Midrij",
        "Al Qarin",
        "Alnabhanya",
        "AlRass",
        "As Sulaimaniyah",
        "As Sulubiayh",
        "Ash Shimasiyah",
        "Ayn Fuhayd",
        "Badaya",
        "Bukeiriah",
        "Buraidah",
        "Dariyah",
        "Duhknah",
        "Dulay Rashid",
        "Kahlah",
        "Midinhab",
        "Onaiza",
        "Oyoon Al Jawa",
        "Qassim",
        "Qbah",
        "Qusayba",
        "Riyadh Al Khabra",
        "Shari",
        "Thebea",
        "Uqlat Al Suqur"
      ],
      [
        "Abu Ajram",
        "Al Laqayit",
        "An Nabk Abu Qasr",
        "Ar Radifah",
        "Ar Rafi'ah",
        "At Tuwayr",
        "Domat Al Jandal",
        "Ghtai",
        "Hadeethah",
        "Hedeb",
        "Jouf",
        "Kara",
        "Qurayat",
        "Sakaka",
        "Suwayr",
        "Tabrjal",
        "Zallum"
      ],
      [
        "Abha",
        "Abha Manhal",
        "Ahad Rufaidah",
        "Al Bashayer",
        "Balahmar",
        "Balasmar",
        "Balqarn",
        "Bareq",
        "Birk",
        "Bisha",
        "Dhahran Al Janoob",
        "Harjah",
        "Khamis Mushait",
        "Majarda",
        "Mohayel Aseer",
        "Namas",
        "Qahmah",
        "Rejal Alma'a",
        "Sabt El Alaya",
        "Sarat Obeida",
        "Tanda",
        "Tanuma",
        "Tatleeth",
        "Turaib",
        "Wadeien",
        "Wadi Bin Hasbal"
      ],
      [
        "Abu Areish",
        "Ahad Masarha",
        "Al Ardah",
        "Al Idabi",
        "Ash Shuqaiq",
        "Bish",
        "Damad",
        "Darb",
        "Farasan",
        "Gizan",
        "Karboos",
        "Sabya",
        "Samtah",
        "Siir"
      ],
      [
        "Al Ajfar",
        "Al Haith",
        "Al Hulayfah As Sufla ",
        "Al Khitah",
        "Al Wasayta",
        "An Nuqrah",
        "Ash Shamli",
        "Ash Shananah",
        "Baqa Ash Sharqiyah",
        "Baqaa",
        "Ghazalah",
        "Hail",
        "Mawqaq",
        "Qufar",
        "Simira"
      ],
      [
        "Adham",
        "Al Moya",
        "Alhada",
        "Amaq",
        "Asfan",
        "Bahara",
        "Hali",
        "Hawea/Taif",
        "Ja'araneh",
        "Jeddah",
        "Jumum",
        "Khulais",
        "Khurma",
        "Laith",
        "Makkah",
        "Mastura",
        "Muthaleif",
        "Nimra",
        "Qouz",
        "Qunfudah",
        "Rabigh",
        "Rania",
        "Shoaiba",
        "Shumeisi",
        "Taif",
        "Towal",
        "Turba",
        "Wadi Fatmah",
        "Zahban"
      ],
      [
        "Hubuna",
        "Najran",
        "Sharourah"
      ],
      [
        "Ad Dahinah",
        "Ad Dubaiyah",
        "Afif",
        "Aflaj",
        "Al Bijadyah",
        "Al Hayathem",
        "Al Hufayyirah",
        "Alghat",
        "Artawiah",
        "Daelim",
        "Dawadmi",
        "Deraab",
        "Dere'iyeh",
        "Dhurma",
        "Hareeq",
        "Hawtat Bani Tamim",
        "Hotat Sudair",
        "Huraymala",
        "Jalajel",
        "Khairan",
        "Khamaseen",
        "Kharj",
        "Layla",
        "Majma",
        "Mrat",
        "Mubayid",
        "Mulayh",
        "Muzahmiah",
        "Oyaynah",
        "Qasab",
        "Quwei'ieh",
        "Remah",
        "Riyadh",
        "Rowdat Sodair",
        "Rvaya Aljamsh",
        "Rwaydah",
        "Sahna",
        "Sajir",
        "Shaqra",
        "Sulaiyl",
        "Tanumah",
        "Tebrak",
        "Thadek",
        "Tharmada",
        "Thumair",
        "Um Aljamajim",
        "Ushayqir",
        "Wadi El Dwaser",
        "Zulfi"
      ],
      [
        "Al Bada",
        "Duba",
        "Halat Ammar",
        "Haqil",
        "Tabuk",
        "Tayma",
        "Umluj",
        "Wajeh (Al Wajh)"
      ],
      [
        "Ain Dar",
        "Al Hassa",
        "Al-Jsh",
        "Anak",
        "Ath Thybiyah",
        "Awamiah",
        "Baqiq",
        "Batha",
        "Dammam",
        "Dhahran",
        "Hafer Al Batin",
        "Harad",
        "Haweyah/Dha",
        "Hofuf",
        "Jafar",
        "Jubail",
        "Khafji",
        "Khobar",
        "Khodaria",
        "King Khalid Military City",
        "Mubaraz",
        "Mulaija",
        "Nabiya",
        "Noweirieh",
        "Ojam",
        "Othmanyah",
        "Qarah",
        "Qariya Al Olaya",
        "Qatif",
        "Qaysoomah",
        "Rahima",
        "Ras Al Kheir",
        "Ras Tanura",
        "Safanyah",
        "Safwa",
        "Salwa",
        "Sarar",
        "Satorp (Jubail Ind'l 2)",
        "Seihat",
        "Tanjeeb",
        "Tarut",
        "Thuqba",
        "Udhaliyah",
        "Uyun"
      ],
      [
        "Arar",
        "Hazm Al Jalamid",
        "Nisab",
        "Rafha",
        "Rawdat Habbas",
        "Turaif"
      ]
    ];
    citiesAr = [
      [
        "العقيق",
        "الأطاولة",
        "الباحة",
        "بلجرشي",
        "قلوه",
        "الحجرة",
        "المندق",
        "المخواة",
        "سبيحة"
      ],
      [
        "العيص",
        "بدر",
        "الحناكية",
        "خيبر",
        "المدينة المنورة",
        "مهد الذهب",
        "العلا",
        "ينبع",
        "ينبع البحر",
        "ينبع النخيل"
      ],
      [
        "ابا الورود",
        "البتراء",
        "الدليمية",
        "الفويلق",
        "الخشيبي",
        "المدرج",
        "القرين",
        "النبهانية",
        "الرس",
        "السليمانية",
        "الصلبيّة",
        "الشماسية",
        "عين فهيد",
        "البدائع",
        "البكيرية",
        "بريدة",
        "ضرية",
        "دخنة",
        "ضليع رشيد",
        "كحله",
        "المذنب",
        "عنيزة",
        "عيون الجواء",
        "القصيم",
        "قبه",
        "قصيباء",
        "رياض الخبراء",
        "شري",
        "الذيبية/ القصيم",
        "عقلة الصقور"
      ],
      [
        "أبو عجرم",
        "اللقائط",
        "النبك أبو قصر",
        "الرديفة",
        "الرفيعة",
        "الطوير",
        "دومة الجندل",
        "غطي",
        "الحديثة",
        "هديب",
        "الجوف",
        "قارا",
        "القريات",
        "سكاكا",
        "صوير",
        "طبرجل",
        "زلوم"
      ],
      [
        "ابها",
        "ابها المنهل",
        "احد رفيده",
        "البشائر",
        "بللحمر",
        "بللسمر",
        "بلقرن",
        "بارق",
        "البرك",
        "بيشة",
        "ظهران الجنوب",
        "الحرجة",
        "خميس مشيط",
        "المجاردة",
        "محايل عسير",
        "النماص",
        "القحمة",
        "رجال ألمع",
        "سبت العلايا",
        "سراة عبيدة",
        "تندحة",
        "تنومة / منطقة عسير",
        "تثليث",
        "طريب",
        "الواديين",
        "وادي بن هشبل"
      ],
      [
        "ابو عريش",
        "أحد المسارحة",
        "العارضة",
        "العيدابي",
        "الشقيق",
        "بيش",
        "ضمد",
        "الدرب",
        "جزر فرسان",
        "جازان",
        "الكربوس",
        "صبيا",
        "صامطة",
        "سر"
      ],
      [
        "الأجفر",
        "الحائط",
        "الحليفة السفلى",
        "الخطة",
        "الوسيطاء",
        "النقرة",
        "الشملي",
        "الشنان",
        "بقعاء الشرقية",
        "بقعاء",
        "الغزالة",
        "حائل",
        "موقق",
        "قفار",
        "سميراء"
      ],
      [
        "أضم",
        "الموية",
        "الهدا",
        "عمق",
        "عسفان",
        "بحرة",
        "حلي",
        "الحوية - الطائف",
        "الجعرانه",
        "جدة",
        "الجموم",
        "خليص",
        "الخرمة",
        "الليث",
        "مكة المكرمة",
        "مستورة",
        "المظيلف",
        "نمره",
        "القوز",
        "القنفذة",
        "رابغ",
        "رنية",
        "الشعيبة",
        "الشميسي",
        "الطائف",
        "ثول",
        "تربة",
        "وادي فاطمه",
        "ذهبان"
      ],
      [
        "حبونا",
        "نجران",
        "شرورة"
      ],
      [
        "الداهنة",
        "الضبيعة",
        "عفيف",
        "الأفلاج",
        "البجادية",
        "الهياثم",
        "الحفيرة",
        "الغاط",
        "الأرطاوية",
        "الدلم",
        "الدوادمي",
        "ديراب",
        "الدرعية",
        "ضرما",
        "الحريق",
        "حوطة بني تميم",
        "حوطة سدير",
        "حريملاء",
        "جلاجل",
        "تمرة",
        "الخماسين",
        "الخرج",
        "ليلى",
        "المجمعة",
        "مرات",
        "مبايض",
        "مليح",
        "المزاحمية",
        "العيينة",
        "القصب",
        "القويعية",
        "رماح",
        "الرياض",
        "روضة سدير",
        "رفائع الجمش",
        "الرويضه",
        "الصحنة",
        "ساجر",
        "شقراء",
        "السليل",
        "تنومة / القصيم",
        "تبراك",
        "ثادق",
        "ثرمداء",
        "تمير",
        "ام الجماجم",
        "اشيقر",
        "وادي الدواسر",
        "الزلفي"
      ],
      [
        "البدع",
        "ضبا",
        "حالة عمار",
        "حقل",
        "تبوك",
        "تيماء",
        "أملج",
        "الوجه"
      ],
      [
        "عرعر",
        "حزم الجلاميد",
        "شعبة نصاب",
        "رفحاء",
        "روضه هباس",
        "طريف"
      ],
    ];
    if(widget.address!=null){
      cubit.addressController.text = widget.address!.shippingAddress1!;
      cubit.postCodeController.text = widget.address!.shippingPostcode!;
      int indexOfRegion = 0;
      if(regionsAr.contains(widget.address!.shippingCountry!)){
        indexOfRegion = regionsAr.indexOf(widget.address!.shippingCountry!);
      }
      selectedRegionIndex = indexOfRegion;
      selectedRegion = regions[indexOfRegion];
      print('dfvvfd $selectedRegion');
      cubit.countryController.text = widget.address!.shippingCountry!;
      selectedCity = widget.address!.shippingCity!;
      cubit.cityController.text = widget.address!.shippingCity!;
      cubit.phoneController.text = widget.address!.shippingPhone!;
      cubit.nameController.text = widget.address!.shippingFirstName!;
      cubit.surNameController.text = widget.address!.shippingLastName!;
      cubit.emailController.text = widget.address!.shippingEmail!;
    }
    setState(() {});
  }
}
