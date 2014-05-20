// ############################################################	//
//	MaestroPanel Lisans API - WHMCS Otomasyon Modülü	//
//	Bilrom Bilişim ve Medya Hiz. San. ve Tic. A.Ş.   	//
// Yazılım Geliştirme ve AR-GE Ekibi tarafından hazırlanmıştır. //
//			www.bilrom.com				//
//		Yayınlanma Tarihi: 12.05.2014			//
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

