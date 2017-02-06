<?php

/**
 * Rutas permitidas por el sistema. 
 */

	\App\Route::get("login", "\Controllers\LoginController@showView","login");
	\App\Route::post("login", "\Controllers\LoginController@login","login");
	
	\App\Route::get("logout", "\Controllers\LoginController@logout","logout");
	
	\App\Route::get("terminosycondiciones", function (){
			\App\Utils::makeView(new \Views\Core\TermsAndConditionsView());
	},"terminos");
	
	\App\Route::get("auction/finished/{keyAuction}", '\Controllers\AuctionController@finishedUserAuction',"auction-finished",'\Security\Auth@authenticityFilter' );
	
	\App\Route::get("principal", function (){
		\App\Utils::makeView(new \Views\Core\PrincipalView());
	},"principal", '\Security\Auth@authenticityFilter');
	
	\App\Route::get("system/date", function (){
		return \App\Utils::getDate();
	},"system-date");
	
	/** +------------------------------------------------------------------------
	 *  [[ MENÚS]]
	 *  +------------------------------------------------------------------------
	 */
	
	//Routes de usuarios
	\App\Route::get("users", "\Controllers\UserController@showView","users", '\Security\Auth@authenticityFilter', \Models\Role::MANAGER);
	\App\Route::get("users/activate/{id}/{activate}", "\Controllers\UserController@activate","users-activate", '\Security\Auth@authenticityFilter');
	\App\Route::post("users/search/page/{page}", "\Controllers\UserController@search","users-search", '\Security\Auth@authenticityFilter');
	\App\Route::get("users/create/edit/{id}", "Controllers\UserController@createEditUserView","users-create-edit", '\Security\Auth@authenticityFilter');
	\App\Route::post("users/create/edit", "\Controllers\UserController@createEditUser","users-create-edit", '\Security\Auth@authenticityFilter');
	
	//Routes de productos
	\App\Route::get("products", "\Controllers\ProductController@showView","products", '\Security\Auth@authenticityFilter', \Models\Role::MANAGER);
	\App\Route::post("products/search/page/{page}", "\Controllers\ProductController@search","products-search", '\Security\Auth@authenticityFilter');
	\App\Route::post("products/create/edit", "\Controllers\ProductController@createEditProduct","products-create-edit", '\Security\Auth@authenticityFilter');
	\App\Route::get("products/create/edit/{keyProduct}", "\Controllers\ProductController@createEditProductView","products-create-edit", '\Security\Auth@authenticityFilter');
	\App\Route::post("products/save/provider/product", "\Controllers\ProductController@saveProviderProduct","save-provider-products",'\Security\Auth@authenticityFilter');
	\App\Route::get("product/{keyProduct}/add/provider/", "\Controllers\ProductController@addProviderProductView","products-add-provider", '\Security\Auth@authenticityFilter');
	\App\Route::post("products/search/provider/product/page/{page}", "\Controllers\ProductController@searchProviderProduct","search-provider-products",'\Security\Auth@authenticityFilter');
	\App\Route::get("product/{keyProduct}/delete/provider/{idProductUser}", "\Controllers\ProductController@deleteProviderProduct","delete-provider-product", '\Security\Auth@authenticityFilter');
	\App\Route::get("products/add/datasheet/{keyProduct}", "\Controllers\ProductController@addDatasheetProductView","products-add-dataSheet", '\Security\Auth@authenticityFilter');
	\App\Route::post("products/add/datasheet/{id}", "\Controllers\ProductController@addDatasheetProduct","products-add-dataSheet", '\Security\Auth@authenticityFilter');
	\App\Route::get("products/download/datasheet/{id}", "\Controllers\ProductController@downloadDatasheet","download-datasheet", '\Security\Auth@authenticityFilter');
	
	//Routes de subasta
	\App\Route::get("auctions", "\Controllers\AuctionController@showView","auctions", '\Security\Auth@authenticityFilter', \Models\Role::ALL_ROLES);
	\App\Route::post("auctions/search/page/{page}", "\Controllers\AuctionController@search","auctions-search", '\Security\Auth@authenticityFilter');
	\App\Route::get("auctions/create/edit/{id}", "\Controllers\AuctionController@createEditAuctionView","auctions-create-edit", '\Security\Auth@authenticityFilter');
	\App\Route::post("auctions/create/edit/new", "\Controllers\AuctionController@createEditAuction","auction-create-edit", '\Security\Auth@authenticityFilter');
	
	\App\Route::get("auctions/confirm/{clave}", "\Controllers\ConfirmController@showView","confirm");
	\App\Route::post("auctions/confirm/{clave}", "\Controllers\ConfirmController@confirmParticipationAuction","confirm-auction");
	\App\Route::get("auctions/information/{clave}", "\Controllers\InformationAuctionController@showView","information-auction", '\Security\Auth@authenticityFilter');
	\App\Route::post("auctions/information/page/{page}", "\Controllers\InformationAuctionController@searchProvidersUsers","search-providers-users", '\Security\Auth@authenticityFilter');
	
	\App\Route::POST("auctions/information/confirm", "\Controllers\ConfirmController@confirmParticipation","information-confirm", '\Security\Auth@authenticityFilter');
	\App\Route::get("auctions/invite/{id}", "\Controllers\InviteProviderAuctionController@showView","invite-providers-auction", '\Security\Auth@authenticityFilter');
	\App\Route::POST("auctions/invite/", "\Controllers\InviteProviderAuctionController@invite","auctions-invite-user", '\Security\Auth@authenticityFilter');
	\App\Route::get("auctions/information/confirm/{idUser}/{idAuction}", "\Controllers\ConfirmController@cancelConfirmationProviderView","cancel-confirmation-provider", '\Security\Auth@authenticityFilter');
	
	\App\Route::get("auctions/questions/{clave}", "\Controllers\QuestionsAuctionController@showView","questions-auction");
	\App\Route::POST("auctions/questions", "\Controllers\QuestionsAuctionController@writeQuestions","questions");	
	\App\Route::get("auctions/answer/{auctionKey}", "\Controllers\AuctionController@showAnswerQuestionsView","answer-question-auction",'\Security\Auth@authenticityFilter');
	\App\Route::POST("auctions/questions/answer", "\Controllers\AuctionController@saveAnswers","save-answer", '\Security\Auth@authenticityFilter');
	\App\Route::get("auctions/cancel/{auctionKey}", "\Controllers\AuctionController@cancelAuctionView","cancel-auction", '\Security\Auth@authenticityFilter');
	\App\Route::POST("auctions/cancel", "\Controllers\AuctionController@cancelAuction","cancel-auction", '\Security\Auth@authenticityFilter');
	
	\App\Route::get("auctions/history/chat/{idAuction}", "\Controllers\AuctionController@historyChat","history-chat", '\Security\Auth@authenticityFilter');
	\App\Route::get("auctions/history/bid/{auctionKey}", "\Controllers\AuctionController@showHistoryBidView","history-bid", '\Security\Auth@authenticityFilter');
	\App\Route::get("auctions/go/{clave}","\Controllers\AuctionController@goAuction","go-reverseauction",'\Security\Auth@authenticityFilter', array(\Models\Role::ALL_ROLES));
	\App\Route::post("auctions/get/chat/", "\Controllers\AuctionController@getChatsPost","auction-chat",'\Security\Auth@authenticityFilter', array(\Models\Role::ALL_ROLES));
	\App\Route::get("auctions/get/chat/{idAuction}/{keyRole}", "\Controllers\AuctionController@getChats","auction-chat", '\Security\Auth@authenticityFilter', \Models\Role::MANAGER);
	\App\Route::get("auctions/download/history/chat/{idAuction}", "\Controllers\AuctionController@downloadHistoryChat","download-history-chat", '\Security\Auth@authenticityFilter');
	\App\Route::get("auctions/download/history/bid/{idAuction}", "\Controllers\AuctionController@downloadHistoryBid","download-history-bid", '\Security\Auth@authenticityFilter');
	\App\Route::get("auctions/analyze", "\Controllers\AnalyzeAuctionController@showAnalyzeAuctionView","auctions-analyze", '\Security\Auth@authenticityFilter');
	\App\Route::post("auctions/analyze", "\Controllers\AnalyzeAuctionController@analyzeBestBids","auctions-analyze-bestBids", '\Security\Auth@authenticityFilter');
	
	// BIDS
	\App\Route::post("auctions/get/bids/", "\Controllers\AuctionBidController@findByAuctionAndUser","auction-bids",'\Security\Auth@authenticityFilter', array(\Models\Role::ALL_ROLES));
	
	
	
?>
