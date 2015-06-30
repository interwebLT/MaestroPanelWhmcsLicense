// ############################################################	//
//	 	MaestroPanel Lisans API - WHMCS Otomasyon Modülü		//
//	 	 Bilrom Bilişim ve Medya Hiz. San. ve Tic. A.Ş.   		//
// Yazılım Geliştirme ve AR-GE Ekibi tarafından hazırlanmıştır. //
//			  			www.bilrom.com				   			//
//				Yayınlanma Tarihi: 12.05.2014					//
//				Son Düzenleme: 26.06.2014						//
// ############################################################	//

MaestroPanel Lisans API / WHMCS Modül Kurulumu

1- modules/ klasörünü WHMCS ana klasörüne upload edin.

2- WHMCS Admin -> Setup -> Addon Modules alanından modülü aktif
ediniz ve "Configure" butonuna tıklayın.

3- API Key'inizi ilgili alana yazın ve modülün test modunda
çalışmasını istiyorsanız ilgili alanı işaretleyin.

4- Yeni bir ürün oluşturarak "Module" tab'ında ilgili ürünün
hangi lisans tipi için geçerli olduğunu seçin.

5- "Custom Fields" tab'ında "Lisans Adı" (büyük küçük harf
duyarlıdır) ile yeni bir "textbox" özellikli alan oluşturun.
Alan özellikleri içerisinde "Required Field" ve "Show on Order
Form" fonksiyonlarını aktif edin.

6- Yeni bir ürün aktivasyon mail şablonu oluşturarak aşağıdaki
bilgileri ekleyin.

Lisans Detayları:
Lisans Kodu: {$service_domain}

7- Müşteri alanı şablonunu aşağıdaki dosya yolundan
değiştirebilirsiniz.

modules/servers/maestropanellisans/clientarea.tpl


#################################################################

v2.0 Değişiklikler
- Addons > MaestroPanel Lisans arayüzü tamamlandı.
- Artık WHMCS ile tüm lisansları yönetebilir, MaestroPanel arayüzüne bağlanmadan yeni lisanslar WHMCS üzerinden oluşturulabilir.
- MaestroPanel Lisans API'ye eklenen tüm yeni fonksiyonlar modüle de entegre edildi. Admin arayüzünden, lisansın otomatik yenilenmesi ve iptal edilmesi mümkün.
- İptal fonksiyonu varsayılan WHMCS fonksiyonu altına, "Terminate" fonksiyonuna entegre edildi.
- clientarea ve admin arayüzüne "Oto. Yenileme Durumu" bilgisi eklendi.
- API erişim bilgileri ile uzayan kod bloğu kısaltıldı.
- Aynı MaestroPanel hesabını birden fazla markada kullanmayı daha rahat hale getirebilmek için lisans isimlerini ayırmakta yararlı olabilecek yeni bir addon parametresi eklendi. "License Name Prefix" bu parametrenin boş bırakılması halinde lisans isimlerine ön ek getirilmeyecektir. Bu parametre ile lisansın hangi markadan kayıt edildiğini ayırt edebilirsiniz.
- Admin arayüzü lisans status düzeltildi.
- BETA: External API Key parametresi eklendi. (Boş bırakınız.)

v1.1 Değişiklikler
- Her kullanıcının birden fazla ücretsiz lisans almasını engelleyen fonkisyon düzeltildi.
- Ürün detayları bölümüne "Boşa Çıkart" butonu ve butona uyarı mesajı eklendi. (clientarea.tpl)
- Modül işlemlerinin tümü loglara kaydedilmesi sağlandı. Admin alanında "Utilities > Logs >  Module Log" admında "Enable Debug Logging" opsiyonunu aktif etmeyi unutmayınız.
- Bazı kurulumlarda lisans tiplerinde Türkçe karakterlerden kaynaklanan sorunlar düzeltildi.

Önemli: Bu güncelleme sonrası modülün çalışmaması halinde lütfen ürün detaylarında Module seçeneğini "None" yaparak kaydediniz. Ardından tekrar "MaestroPanelLisans" modülü seçerek modül ayarlarını yapınız.