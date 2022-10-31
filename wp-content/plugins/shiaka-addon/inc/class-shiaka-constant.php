<?php

namespace Shiaka;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

class Constant
{
    protected static $instance = null;

    protected $prefex = 'store_location_';

    /**
     * Initiator
     *
     * @return object
     * @since 1.0.0
     */
    public static function instance()
    {
        if (!isset(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }


    public static function get_stores_location()
    {
        return self::$store_address_cities;
    }


    public static array $states = [
        'NLA' => [
            'name' => [
                'ar' => 'منطقة الحدود الشمالية',
                'en' => 'Northern Borders Region	'
            ],
            'cites' => [
                'ar' => [
                    'عرعر',
                    'حزم الجلاميد',
                    'طريف',
                    'رفحاء',
                    'روضة هباس',
                    'شعبة نصاب',
                ],
                'en' => [
                    'arar',
                    'hazam al jalamid',
                    'turaif',
                    'rafha',
                    'Nasib'
                ]
            ],
            'code' => 'north line area [NLA]'
        ],
        'MCR' => [
            'name' => [
                'ar' => 'منطقة مكة المكرمة',
                'en' => 'Mecca Region'
            ],
            'cites' => [
                'ar' => [
                    'الجموم',
                    'مكة المكرمة',
                    'الشميسي',
                    'وادي فاطمة',
                    'عسفان',
                ],
                'en' => [
                    'Ja/\'araneh',
                    'Jumum',
                    'Makkah',
                    'Shumeisi',
                    'Wadi Fatmah',
                    'Asfan',
                ]
            ],
            'code' => 'Mecca Region	'
        ],

        "RID" => [
            'code' => 'Riada',
            'name' => [
                'ar' => 'الرياض',
                'en' => 'Riyad'
            ],
            'cites' => [
                "ar" => array("عفيف", "البجادية", "الحفيرة", "الدوادمي", "رفائع الجمش", "ساجر", "الداهنة", "الغاط", "الأرطاوية", "حوطة سدير", "جلاجل", "المجمعة", "مرات", "مبايض", "مليح", "القصب", "روضة سدير", "شقراء", "تنومة / القصيم", "ثرمداء", "تمير", "ام الجماجم", "اشيقر", "الزلفي", "الضبيعة", "الهياثم", "الدلم", "الحريق", "حوطة بني تميم", "الخرج", "الصحنة", "ديراب", "الدرعية", "ضرما", "حريملاء", "المزاحمية", "العيينة", "القويعية", "رماح", "الرياض", "الرويضه", "تبراك", "ثادق", "الأفلاج", "تمرة", "الخماسين", "ليلى", "السليل", "وادي الدواسر"),
                "en" => array("Afif", "Al Bijadyah", "Al Hufayyirah", "Dawadmi", "Rvaya Aljamsh", "Sajir", "Ad Dahinah", "Alghat", "Artawiah", "Hotat Sudair", "Jalajel", "Majma", "Mrat", "Mubayid", "Mulayh", "Qasab", "Rowdat Sodair", "Shaqra", "Tanumah", "Tharmada", "Thumair", "Um Aljamajim", "Ushayqir", "Zulfi", "Ad Dubaiyah", "Al Hayathem", "Daelim", "Hareeq", "Hawtat Bani Tamim", "Kharj", "Sahna", "Deraab", "Dere'iyeh", "Dhurma", "Huraymala", "Muzahmiah", "Oyaynah", "Quwei'ieh", "Remah", "Riyadh", "Rwaydah", "Tebrak", "Thadek", "Aflaj", "Khairan", "Khamaseen", "Layla", "Sulaiyl", "Wadi El Dwaser")
            ]
        ],
        "ALQ" => [
            'code' => 'Alqasim',
            'name' => [
                'ar' => 'منطقة القصيم',
                'en' => 'Alqasim region'
            ],
            'cites' => [
                'ar' => array("القرين", "ابا الورود", "البتراء", "الدليمية", "الفويلق", "الخشيبي", "المدرج", "النبهانية", "الرس", "الصلبيّة", "الشماسية", "عين فهيد", "البدائع", "البكيرية", "بريدة", "ضرية", "دخنة", "ضليع رشيد", "كحله", "المذنب", "عنيزة", "عيون الجواء", "القصيم", "قبه", "قصيباء", "رياض الخبراء", "شري", "الذيبية/ القصيم", "عقلة الصقور", "السليمانية"),
                "en" => array("Al Qarin", "Aba Alworood", "Al Batra", "Al Dalemya", "Al Fuwaileq / Ar Rishawiyah", "Al Khishaybi", "Al Midrij", "Alnabhanya", "AlRass", "As Sulubiayh", "Ash Shimasiyah", "Ayn Fuhayd", "Badaya", "Bukeiriah", "Buraidah", "Dariyah", "Duhknah", "Dulay Rashid", "Kahlah", "Midinhab", "Onaiza", "Oyoon Al Jawa", "Qassim", "Qbah", "Qusayba", "Riyadh Al Khabra", "Shari", "Thebea", "Uqlat Al Suqur", "As Sulaimaniyah")
            ]
        ],
        "ASIR" => [
            'code' => 'ASIR',
            'name' => [
                'ar' => 'منطقة العسير',
                'en' => 'Asir region'
            ],
            'cites' => [
                'ar' => array("ابها", "ابها المنهل", "احد رفيده", "بللحمر", "بللسمر", "بلقرن", "بارق", "ظهران الجنوب", "الحرجة", "خميس مشيط", "المجاردة", "محايل عسير", "النماص", "رجال ألمع", "سبت العلايا", "سراة عبيدة", "تندحة", "تنومة / منطقة عسير", "تثليث", "طريب", "الواديين", "وادي بن هشبل", "البشائر", "البرك", "بيشة", "القحمة"),
                "en" => array("Abha", "Abha Manhal", "Ahad Rufaidah", "Balahmar", "Balasmar", "Balqarn", "Bareq", "Dhahran Al Janoob", "Harjah", "Khamis Mushait", "Majarda", "Mohayel Aseer", "Namas", "Rejal Alma'a", "Sabt El Alaya", "Sarat Obeida", "Tanda", "Tanuma", "Tatleeth", "Turaib", "Wadeien", "Wadi Bin Hasbal", "Al Bashayer", "Birk", "Bisha", "Qahmah")
            ]
        ],
        "LITC" => [
            'code' => 'MDE',
            'name' => [
                'ar' => 'منطقة المدينة المنورة',
                'en' => 'Medina area'
            ],
            'cites' => [
                'ar' => array("بدر", "الحناكية", "خيبر", "المدينة المنورة", "مهد الذهب", "العلا", "ينبع", "ينبع البحر", "ينبع النخيل", "العيص"),
                "en" => array("Bader", "Hinakeya", "Khaibar", "Madinah", "Mahad Al Dahab", "Oula", "Yanbu", "Yanbu Al Baher", "Yanbu Nakhil", "Al Ais")
            ]
        ],

        "ALBH" => [
            'code' => 'ALBAHA',
            'name' => [
                "ar" => 'منطقة الباحة',
                'en' => 'Al Baha area'
            ],
            'cites' => [
                'ar' => array("سبيحة", "العقيق", "الباحة", "بلجرشي", "قلوه", "المندق", "المخواة", "الحجرة", "الأطاولة"),
                "en" => array("Subheka", "Aqiq", "Baha", "BilJurashi", "Gilwa", "Mandak", "Mikhwa", "Hajrah", "Atawleh")
            ]
        ],
        "JAZAN" => [
            'code' => 'JAZAN',
            'name' => [
                "ar" => 'منطقة جازان ',
                'en' => 'Jazan area'
            ],
            'cites' => [
                'ar' => array("ابو عريش", "أحد المسارحة", "العارضة", "العيدابي", "الشقيق", "بيش", "ضمد", "الدرب", "جزر فرسان", "جازان", "الكربوس", "صبيا", "صامطة", "سر"),
                "en" => array("Abu Areish", "Ahad Masarha", "Al Ardah", "Al Idabi", "Ash Shuqaiq", "Bish", "Damad", "Darb", "Farasan", "Gizan", "Karboos", "Sabya", "Samtah", "Siir")
            ]
        ],
        "NAJRAN" => [
            'code' => 'NAJRAN',
            'name' => [
                "ar" => 'منطقة نجران ',
                'en' => 'Najran area'
            ],
            'cites' => [
                'ar' => array("حبونا", "نجران", "شرورة"),
                "en" => array("Hubuna", "Najran", "Sharourah")
            ]
        ],
        "ALJ" => [
            'code' => 'ALJOUF',
            'name' => [
                "ar" => 'منطقة الجوف ',
                'en' => 'Aljouf area'
            ],
            'cites' => [
                'ar' => array("أبو عجرم", "اللقائط", "النبك أبو قصر", "الرديفة", "الرفيعة", "الطوير", "دومة الجندل", "هديب", "الجوف", "قارا", "سكاكا", "صوير", "طبرجل", "زلوم", "غطي", "الحديثة", "القريات"),
                "en" => array("Abu Ajram", "Al Laqayit", "An Nabk Abu Qasr", "Ar Radifah", "Ar Rafi'ah", "At Tuwayr", "Domat Al Jandal", "Hedeb", "Jouf", "Kara", "Sakaka", "Suwayr", "Tabrjal", "Zallum", "Ghtai", "Hadeethah", "Qurayat")
            ]
        ],

        "TPK" => [
            'code' => 'TABOUK',
            'name' => [
                "ar" => 'منطقة تبوك ',
                'en' => 'Tabouk area'
            ],
            'cites' => [
                "ar" => array("البدع", "ضبا", "حالة عمار", "حقل", "تبوك", "تيماء", "الوجه", "أملج"),
                "en" => array("Al Bada", "Duba", "Halat Ammar", "Haqil", "Tabuk", "Tayma", "Wajeh (Al Wajh)", "Umluj")
            ]
        ],

        "HAL" => [
            'code' => 'HAAL',
            'name' => [
                "ar" => 'منطقة حائل ',
                'en' => 'Haeel area'
            ],
            'cites' => [
                "ar" => array("الأجفر", "الحائط", "الحليفة السفلى", "الخطة", "الوسيطاء", "النقرة", "الشملي", "الشنان", "بقعاء الشرقية", "بقعاء", "الغزالة", "حائل", "موقق", "قفار", "سميراء"),
                'en' => array("Al Ajfar", "Al Haith", "Al Hulayfah As Sufla", "Al Khitah", "Al Wasayta", "An Nuqrah", "Ash Shamli", "Ash Shananah", "Baqa Ash Sharqiyah", "Baqaa", "Ghazalah", "Hail", "Mawqaq", "Qufar", "Simira")
            ]
        ],


    ];


    public static $store_address_cities = [
        'ar' => [
            'أبها ',
            'الخرج',
            'الدمام',
            'الرياض',
            'الطائف ',
            'القنفذة ',
            'المدينة المنورة',
            'الهفوف',
            'بريدة',
            'بلجرشي',
            'تبوك',
            'جدة',
            'جيزان',
            'حائل',
            'خميس مشيط',
            'محايل عسير',
            'مكة المكرمة',
            'ينبع'
        ],
        'en' => [
            'Abha',
            'Al Qunfudhah',
            'Alkharj',
            'Baljurashi',
            'Buraydah',
            'Damam',
            'Hofuf',
            'Jeddah',
            'Jizan',
            'Khamees Musheet',
            'Madinah',
            'Mahael Aseer',
            'Makkah',
            'Riyadh',
            'Tabuk ',
            'Taif',
            'Unaizah',
            'Yanbu'
        ]
    ];

}