class Translator
{
	static lang = 'en';

	constructor()
	{
		if (!Translator._instance) {
			Translator._instance = this
		}

		return Translator._instance
	}

	static getInstance()
	{
		return this._instance
	}

	static setLang(lang) {
		this.lang = lang
		if (eval('typeof ' + this.lang) == 'undefined'){
			this.lang = "en"
		}
		return this.getInstance();
	}

	static str(str){
		var retStr = eval('eval(this.lang).' + str)
		if (typeof retStr != 'undefined'){
			return retStr
		} else {
			return str
		}
	}

	getLang(){
		return this.lang.slice(-2)
	}
}

var en = {
	Save:"Saved!",
	Welcome: "Hello!",
	Home: "Home page",
	TodosList: "Todos list",
	UserProfil: "User profil",
	UploadFile: "File upload",
	ExternalPage: "External page"
};

var jp = {
	Save:"保存しました",
	Welcome: "こんにちは!",
	Home: "ホームページ",
	TodosList: "イベントリスト",
	UserProfil: "ユーザープロファイル",
	UploadFile: "ファイルのアップロード",
	ExternalPage: "外部ページ"
};

/*
Translator.setLang("jp");
console.log("Lang: ", Translator.getStr("Welcome"));

Translator.setLang("en");
console.log("Lang: ", Translator.getStr("Welcome"));
*/