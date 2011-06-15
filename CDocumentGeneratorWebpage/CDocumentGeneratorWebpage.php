<?php
/* 
CDocumentGeneratorWebpage - Class for creating webpage for docgen.
Copyright (C) 2011 Aleksi Räsänen <aleksi.rasanen@runosydan.net>

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU Affero General Public License as
published by the Free Software Foundation, either version 3 of the
License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU Affero General Public License for more details.

You should have received a copy of the GNU Affero General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

	require 'CHTML/CHTML.php';

	// ************************************************** 
	//  CDocumentGeneratorWebpage
	/*!
		@brief Class for creating a website for document
		  generator.
		@author Aleksi Räsänen
		@email aleksi.rasanen@runosydan.net
	*/
	// ************************************************** 
	class CDocumentGeneratorWebpage
	{
		private $cHTML;
		private $current_page;

		public function __construct()
		{
			$possible_pages = array( 'main', 'about' );
			$this->current_page = 'main';

			if( isset( $_GET['page'] ) )
			{
				if( in_array( $_GET['page'], $possible_pages ) )
					$this->current_page = $_GET['page'];
			}
		}

		// ************************************************** 
		//  createPage
		/*!
			@brief Main function. Creates a web page for user.
		*/
		// ************************************************** 
		public function createPage()
		{
			$this->cHTML = new CHTML();
			$c = $this->cHTML;
			$c->setCSS( 'main.css' );

			$str = $c->createSiteTop( 'Document generator' );
			$str .= $this->createTopLogo();
			$str .= $this->createTopMenu();
			$str .= $c->createSiteBottom();

			echo $str;
		}

		// ************************************************** 
		//  createTopLogo
		/*!
			@brief Creates a site logo div
			@return HTML String
		*/
		// ************************************************** 
		private function createTopLogo()
		{
			$text = 'Document generator';
			$this->cHTML->setExtraParams( array( 'id' =>'top_logo' ) );
			return $this->cHTML->createDiv( $text );
		}

		// ************************************************** 
		//  createTopMenu
		/*!
			@brief Create top menu for site
			@return HTML String
		*/
		// ************************************************** 
		private function createTopMenu()
		{
			$c = $this->cHTML;
			$class = 'top_menu_item';

			if( $this->current_page == 'main' )
				$class = 'top_menu_item_selected';
			
			$link = $c->createLink( 'index.php?page=main', 'MAIN' );
			$c->setExtraParams( array( 'class' => $class ) );
			$text = $c->createSpan( $link );

			if( $this->current_page == 'about' )
				$class = 'top_menu_item_selected';
			else 
				$class = 'top_menu_item';

			$link = $c->createLink( 'index.php?page=about', 'ABOUT' );
			$c->setExtraParams( array( 'class' => $class ) );
			$text .= $c->createSpan( $link );

			$c->setExtraParams( array( 'class' => 'top_menu' ) );
			return $this->cHTML->createDiv( $text );
		}
	}
 
?>
