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
	require 'CForm/CForm.php';
	require 'CHTMLDocumentGenerator/CHTMLDocumentGenerator.php';
	require 'CGeneral/CGeneral.php';

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
		private $post_params;
		private $files_params;
		private $generated_document;
		private $generated_filename;
		private $documents_path;

		// ************************************************** 
		//  __construct
		/*!
			@brief Initializes class private variables
		*/
		// ************************************************** 
		public function __construct()
		{
			$this->post_params = $_POST;
			$this->files_params = $_FILES;
			$this->generated_document = '';
			$this->documents_path = 'generated_documents/';

			$possible_pages = array( 'main', 'about' );
			$this->current_page = 'main';

			if( isset( $_GET['page'] ) )
			{
				if( in_array( $_GET['page'], $possible_pages ) )
					$this->current_page = $_GET['page'];
			}

			$ret = $this->handleFilesParams();
			if(! empty( $ret ) )
			{
				$filename = $this->writeDocumentToFile( $ret );
				$this->generated_filename = $filename;
				$this->current_page = 'document';
			}
		}

		// ************************************************** 
		//  writeDocumentToFile
		/*!
			@brief Write generated document to the file
			@param $document Generated HTML document
			@return Filename in string
		*/
		// ************************************************** 
		private function writeDocumentToFile( $document )
		{
			$cGeneral = new CGeneral();
			$random_string = time() . $cGeneral->createRandomString( 4 );

			$fh = fopen( $this->documents_path . $random_string 
				. '.html', 'w' );
			fwrite( $fh, $document );
			fclose( $fh );

			return $random_string . '.html';
		}

		// ************************************************** 
		//  handleFilesParams
		/*!
			@brief Checks if there was files sent. If so,
				generate document for sent file.

			@return If there was file sent, we return a 
			  HTML document. Otherwise we return empty string.
		*/
		// ************************************************** 
		private function handleFilesParams()
		{
			if(! isset( $this->files_params['file_select']['tmp_name'] ) )
				return;

			if( empty( $this->files_params['file_select']['tmp_name'] ) )
				return;

			$filename = $this->files_params['file_select']['tmp_name'];
			$cHTMLDocumentGenerator = new CHTMLDocumentGenerator( 
				$filename );
			$document = $cHTMLDocumentGenerator->createHTMLDocument();
			
			return $document;
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
			$str .= $this->createPageContent();
			$str .= $c->createSiteBottom();

			return $str;
		}

		// ************************************************** 
		//  createPageContent
		/*!
			@brief Detects what page should be created
			  and then calls the correct method which
			  will create the required page.
		*/
		// ************************************************** 
		private function createPageContent()
		{
			switch( $this->current_page )
			{
				case 'document':
					$ret = $this->createPageShowDocument();
					break;

				case 'main':
					$ret = $this->createPageMain();
					break;

				case 'about';
					$ret = $this->createPageAbout();
					break;
			}

			return $ret;
		}


		// ************************************************** 
		//  createPageShowDocument
		/*!
			@brief Shows generated document
		*/
		// ************************************************** 
		private function createPageShowDocument()
		{
			$c = $this->cHTML;
			$c->setExtraParams( array( 'target' => '_blank' ) );
			$link = $c->createLink( $this->documents_path 
				. $this->generated_filename,
				'Click here to see generated document' );

			$text = $c->createP( 'Your document has been generated.' );
			$c->setExtraParams( array( 'class' => 'site_content' ) );
			$div = $c->createDiv( $text . $link );

			return $div;
		}

		// ************************************************** 
		//  createPageMain
		/*!
			@brief Creates actual content for page 'Main'
			@return HTML String
		*/
		// ************************************************** 
		private function createPageMain()
		{
			$c = $this->cHTML;
			$cForm = new CForm( 'index', 'file' );
			$cForm->addFileButton( 'Select file...', 'file_select' );
			$cForm->addSubmit( 'Create document', 'submit' );
			$form = $cForm->createForm();
			
			$text = 'Select the file for document generating.';
			$p = $c->createP( $text );
			$c->setExtraParams( array( 'class' => 'site_content' ) );
			$div = $c->createDiv( $p . $form );

			return $div;
		}

		// ************************************************** 
		//  createPageAbout
		/*!
			@brief Creates actual content for page 'About'
			@return HTML String
		*/
		// ************************************************** 
		private function createPageAbout()
		{
			$c = $this->cHTML;

			$c->setExtraParams( array( 'target' => '_blank' ) );
			$example_output = $c->createLink(
				'http://s.runosydan.net/nmYi',
				'Click here to see an example output' );

			$c->setExtraParams( array( 'target' => '_blank' ) );
			$github_url = $c->createLink(
				'https://github.com/stargazers/DocumentGeneratorWebpage',
				'Sourcecodes in GitHub' );

			$author = 'Written by Aleksi Räsänen, 2011.';
			$author_email = $c->createLink( 
				'mailto:aleksi.rasanen@runosydan.net',
				'aleksi.rasanen@runosydan.net' );

			$license = 'GNU AGPL v3';
			$about = 'Document generator is very simple source code '
				. 'documentation tool. It uses Doxygen styled tags, '
				. 'but it is not as versatile as Doxygen is. '
				. 'I was too lazy to create Doxyfiles so I made a own '
				. 'document generator what does not require any '
				. 'configuration files. Later I made this '
				. 'website where I can just send files which have '
				. 'commented correctly and it will generate a '
				. 'documentation file for me.';

			$about2 = 'Note that this documentation tool does not '
				. 'check anything about your code except is current '
				. 'comment block for class, private mehod or public '
				. 'method. It does not check if the functions exists '
				. 'or not, it just reads comments from code and '
				. 'generate documents based on those without caring if '
				. 'params are wrong or missing.';

			$privacy = 'This web tool does not store sent source file, '
				. 'it only reads its content and generates the '
				. 'document. If you still do not want to send any '
				. 'files here, just download all the sources from '
				. 'github to your own server and run this softare '
				. 'on your own server.';

			$tags = 'Currently we support tags: @brief, @param, '
				. '@return, @author, @email and @license.';

			$example = '// ********************' . "\n"
				. '// @brief Test method '. "\n"
				. '// @param $foo Parameter called foo ' . "\n"
				. '// @return HTML String ' . "\n"
				. '// ********************' . "\n"
				. 'private function testMetod( $foo )';

			$text = $c->createH( 1, 'Example output' );
			$text .= $example_output;
			$text .= $c->createH( 1, 'Sourcecodes' );
			$text .= $github_url;
			$text .= $c->createH( 1, 'Author' );
			$text .= $c->createP( $author );
			$text .= $author_email;
			$text .= $c->createH( 1, 'License' );
			$text .= $c->createP( $license );
			$text .= $c->createH( 1, 'About' );
			$text .= $c->createP( $about );
			$text .= $c->createP( $about2 );
			$text .= $c->createH( 1, 'Privacy' );
			$text .= $c->createP( $privacy );
			$text .= $c->createH( 1, 'Tags' );
			$text .= $c->createP( $tags );
			$text .= $c->createH( 1, 'Example formatting style' );
			$text .= $c->createPre( $example );

			$c->setExtraParams( array( 'class' => 'about_site_content' ) );
			$div = $c->createDiv( $text );
			return $div;
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
