class CategoriesModel {
  int? id;
  String? name;
  String? slug;
  int? parent;
  String? description;
  String? display;
  int? menuOrder;
  int? count;
  Translations? translations;
  String? lang;
  Links? links;
  Image? image;

  CategoriesModel({this.id, this.name, this.slug, this.parent, this.description, this.display, this.menuOrder, this.count, this.translations, this.lang, this.links, this.image});

  CategoriesModel.fromJson(Map<String, dynamic> json) {
    id = json["id"];
    name = json["name"];
    slug = json["slug"];
    parent = json["parent"];
    description = json["description"];
    display = json["display"];
    menuOrder = json["menu_order"];
    count = json["count"];
    translations = json["translations"] == null ? null : Translations.fromJson(json["translations"]);
    lang = json["lang"];
    links = json["_links"] == null ? null : Links.fromJson(json["_links"]);
    image = json["image"] == null ? null : Image.fromJson(json["image"]);
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data["id"] = id;
    data["name"] = name;
    data["slug"] = slug;
    data["parent"] = parent;
    data["description"] = description;
    data["display"] = display;
    data["menu_order"] = menuOrder;
    data["count"] = count;
    if(translations != null) {
      data["translations"] = translations?.toJson();
    }
    data["lang"] = lang;
    if(links != null) {
      data["_links"] = links?.toJson();
    }
    if(image != null) {
      data["image"] = image?.toJson();
    }
    return data;
  }
}

class Image {
  int? id;
  String? dateCreated;
  String? dateCreatedGmt;
  String? dateModified;
  String? dateModifiedGmt;
  String? src;
  String? name;
  String? alt;

  Image({this.id, this.dateCreated, this.dateCreatedGmt, this.dateModified, this.dateModifiedGmt, this.src, this.name, this.alt});

  Image.fromJson(Map<String, dynamic> json) {
    id = json["id"];
    dateCreated = json["date_created"];
    dateCreatedGmt = json["date_created_gmt"];
    dateModified = json["date_modified"];
    dateModifiedGmt = json["date_modified_gmt"];
    src = json["src"];
    name = json["name"];
    alt = json["alt"];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data["id"] = id;
    data["date_created"] = dateCreated;
    data["date_created_gmt"] = dateCreatedGmt;
    data["date_modified"] = dateModified;
    data["date_modified_gmt"] = dateModifiedGmt;
    data["src"] = src;
    data["name"] = name;
    data["alt"] = alt;
    return data;
  }
}

class Links {
  List<Self>? self;
  List<Collection>? collection;
  List<Up>? up;

  Links({this.self, this.collection, this.up});

  Links.fromJson(Map<String, dynamic> json) {
    self = json["self"]==null ? null : (json["self"] as List).map((e)=>Self.fromJson(e)).toList();
    collection = json["collection"]==null ? null : (json["collection"] as List).map((e)=>Collection.fromJson(e)).toList();
    up = json["up"]==null ? null : (json["up"] as List).map((e)=>Up.fromJson(e)).toList();
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    if(self != null) {
      data["self"] = self?.map((e)=>e.toJson()).toList();
    }
    if(collection != null) {
      data["collection"] = collection?.map((e)=>e.toJson()).toList();
    }
    if(up != null) {
      data["up"] = up?.map((e)=>e.toJson()).toList();
    }
    return data;
  }
}

class Up {
  String? href;

  Up({this.href});

  Up.fromJson(Map<String, dynamic> json) {
    href = json["href"];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data["href"] = href;
    return data;
  }
}

class Collection {
  String? href;

  Collection({this.href});

  Collection.fromJson(Map<String, dynamic> json) {
    href = json["href"];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data["href"] = href;
    return data;
  }
}

class Self {
  String? href;

  Self({this.href});

  Self.fromJson(Map<String, dynamic> json) {
    href = json["href"];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data["href"] = href;
    return data;
  }
}

class Translations {
  int? ar;
  int? en;

  Translations({this.ar, this.en});

  Translations.fromJson(Map<String, dynamic> json) {
    ar = json["ar"];
    en = json["en"];
  }

  Map<String, dynamic> toJson() {
    final Map<String, dynamic> data = <String, dynamic>{};
    data["ar"] = ar;
    data["en"] = en;
    return data;
  }
}