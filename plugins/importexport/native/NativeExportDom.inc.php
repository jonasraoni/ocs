<?php

/**
 * @file NativeExportDom.inc.php
 *
 * Copyright (c) 2000-2012 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class NativeExportDom
 * @ingroup plugins_importexport_native
 *
 * @brief Native import/export plugin DOM functions for export
 */

//$Id$

import('xml.XMLCustomWriter');

class NativeExportDom {
	function &generateTrackDom(&$doc, &$schedConf, &$track) {
		$root =& XMLCustomWriter::createElement($doc, 'track');

		if (is_array($track->getTitle(null))) foreach ($track->getTitle(null) as $locale => $title) {
			$titleNode =& XMLCustomWriter::createChildWithText($doc, $root, 'title', $title, false);
			if ($titleNode) XMLCustomWriter::setAttribute($titleNode, 'locale', $locale);
			unset($titleNode);
		}

		if (is_array($track->getAbbrev(null))) foreach ($track->getAbbrev(null) as $locale => $abbrev) {
			$abbrevNode =& XMLCustomWriter::createChildWithText($doc, $root, 'abbrev', $abbrev, false);
			if ($abbrevNode) XMLCustomWriter::setAttribute($abbrevNode, 'locale', $locale);
			unset($abbrevNode);
		}
		
		if (is_array($track->getIdentifyType(null))) foreach ($track->getIdentifyType(null) as $locale => $identifyType) {
			$identifyTypeNode =& XMLCustomWriter::createChildWithText($doc, $root, 'identify_type', $identifyType, false);
			if ($identifyTypeNode) XMLCustomWriter::setAttribute($identifyTypeNode, 'locale', $locale);
			unset($identifyTypeNode);
		}

		if (is_array($track->getPolicy(null))) foreach ($track->getPolicy(null) as $locale => $policy) {
			$policyNode =& XMLCustomWriter::createChildWithText($doc, $root, 'policy', $policy, false);
			if ($policyNode) XMLCustomWriter::setAttribute($policyNode, 'locale', $locale);
			unset($policyNode);
		}

		$publishedPaperDao =& DAORegistry::getDAO('PublishedPaperDAO');
		foreach ($publishedPaperDao->getPublishedPapersByTrackId($track->getId(), $schedConf->getId()) as $paper) {
			$paperNode =& NativeExportDom::generatePaperDom($doc, $schedConf, $track, $paper);
			XMLCustomWriter::appendChild($root, $paperNode);
			unset($paperNode);
		}

		return $root;
	}

	function &generatePaperDom(&$doc, &$schedConf, &$track, &$paper, $opts = array()) {
		$root =& XMLCustomWriter::createElement($doc, 'paper');

		/* --- PaperID --- */
		XMLCustomWriter::createChildWithText($doc, $root, 'id', $paper->getId());

		/* --- Titles and Abstracts --- */
		if (is_array($paper->getTitle(null))) foreach ($paper->getTitle(null) as $locale => $title) {
			$titleNode =& XMLCustomWriter::createChildWithText($doc, $root, 'title', $title, false);
			if ($titleNode) XMLCustomWriter::setAttribute($titleNode, 'locale', $locale);
			unset($titleNode);
		}

		if (is_array($paper->getAbstract(null))) foreach ($paper->getAbstract(null) as $locale => $abstract) {
			$abstractNode =& XMLCustomWriter::createChildWithText($doc, $root, 'abstract', $abstract, false);
			if ($abstractNode) XMLCustomWriter::setAttribute($abstractNode, 'locale', $locale);
			unset($abstractNode);
		}

		/* --- Indexing --- */

		$indexingNode =& XMLCustomWriter::createElement($doc, 'indexing');
		$isIndexingNecessary = false;

		if (is_array($paper->getDiscipline(null))) foreach ($paper->getDiscipline(null) as $locale => $discipline) {
			$disciplineNode =& XMLCustomWriter::createChildWithText($doc, $indexingNode, 'discipline', $discipline, false);
			if ($disciplineNode) {
				XMLCustomWriter::setAttribute($disciplineNode, 'locale', $locale);
				$isIndexingNecessary = true;
			}
			unset($disciplineNode);
		}
		if (is_array($paper->getType(null))) foreach ($paper->getType(null) as $locale => $type) {
			$typeNode =& XMLCustomWriter::createChildWithText($doc, $indexingNode, 'type', $type, false);
			if ($typeNode) {
				XMLCustomWriter::setAttribute($typeNode, 'locale', $locale);
				$isIndexingNecessary = true;
			}
			unset($typeNode);
		}
		if (is_array($paper->getSubject(null))) foreach ($paper->getSubject(null) as $locale => $subject) {
			$subjectNode =& XMLCustomWriter::createChildWithText($doc, $indexingNode, 'subject', $subject, false);
			if ($subjectNode) {
				XMLCustomWriter::setAttribute($subjectNode, 'locale', $locale);
				$isIndexingNecessary = true;
			}
			unset($subjectNode);
		}
		if (is_array($paper->getSubjectClass(null))) foreach ($paper->getSubjectClass(null) as $locale => $subjectClass) {
			$subjectClassNode =& XMLCustomWriter::createChildWithText($doc, $indexingNode, 'subject_class', $subjectClass, false);
			if ($subjectClassNode) {
				XMLCustomWriter::setAttribute($subjectClassNode, 'locale', $locale);
				$isIndexingNecessary = true;
			}
			unset($subjectClassNode);
		}

		$coverageNode =& XMLCustomWriter::createElement($doc, 'coverage');
		$isCoverageNecessary = false;

		if (is_array($paper->getCoverageGeo(null))) foreach ($paper->getCoverageGeo(null) as $locale => $geographical) {
			$geographicalNode =& XMLCustomWriter::createChildWithText($doc, $coverageNode, 'geographical', $geographical, false);
			if ($geographicalNode) {
				XMLCustomWriter::setAttribute($geographicalNode, 'locale', $locale);
				$isCoverageNecessary = true;
			}
			unset($geographicalNode);
		}
		if (is_array($paper->getCoverageChron(null))) foreach ($paper->getCoverageChron(null) as $locale => $chronological) {
			$chronologicalNode =& XMLCustomWriter::createChildWithText($doc, $coverageNode, 'chronological', $chronological, false);
			if ($chronologicalNode) {
				XMLCustomWriter::setAttribute($chronologicalNode, 'locale', $locale);
				$isCoverageNecessary = true;
			}
			unset($chronologicalNode);
		}
		if (is_array($paper->getCoverageSample(null))) foreach ($paper->getCoverageSample(null) as $locale => $sample) {
			$sampleNode =& XMLCustomWriter::createChildWithText($doc, $coverageNode, 'sample', $sample, false);
			if ($sampleNode) {
				XMLCustomWriter::setAttribute($sampleNode, 'locale', $locale);
				$isCoverageNecessary = true;
			}
			unset($sampleNode);
		}

		if ($isCoverageNecessary) {
			XMLCustomWriter::appendChild($indexingNode, $coverageNode);
			$isIndexingNecessary = true;
		}

		if ($isIndexingNecessary) XMLCustomWriter::appendChild($root, $indexingNode);

		/* --- */

		/* --- Authors --- */

		foreach ($paper->getAuthors() as $author) {
			$authorNode =& NativeExportDom::generateAuthorDom($doc, $schedConf, $paper, $author);
			XMLCustomWriter::appendChild($root, $authorNode);
			unset($authorNode);
		}

		/* --- */

		XMLCustomWriter::createChildWithText($doc, $root, 'pages', $paper->getPages(), false);

		XMLCustomWriter::createChildWithText($doc, $root, 'date_published', NativeExportDom::formatDate($paper->getDatePublished()), false);


		/* --- Galleys --- */
		foreach ($paper->getGalleys() as $galley) {
			$galleyNode =& NativeExportDom::generateGalleyDom($doc, $schedConf, $paper, $galley, $opts);
			if ($galleyNode !== null) XMLCustomWriter::appendChild($root, $galleyNode);
			unset($galleyNode);

		}

		/* --- Supplementary Files --- */
		foreach ($paper->getSuppFiles() as $suppFile) {
			$suppNode =& NativeExportDom::generateSuppFileDom($doc, $schedConf, $paper, $suppFile);
			if ($suppNode !== null) XMLCustomWriter::appendChild($root, $suppNode);
			unset($suppNode);			
		}

		return $root;
	}

	function &generateAuthorDom(&$doc, &$schedConf, &$paper, &$author) {
		$root =& XMLCustomWriter::createElement($doc, 'author');
		if ($author->getPrimaryContact()) XMLCustomWriter::setAttribute($root, 'primary_contact', 'true');

		XMLCustomWriter::createChildWithText($doc, $root, 'firstname', $author->getFirstName());
		XMLCustomWriter::createChildWithText($doc, $root, 'middlename', $author->getMiddleName(), false);
		XMLCustomWriter::createChildWithText($doc, $root, 'lastname', $author->getLastName());

		XMLCustomWriter::createChildWithText($doc, $root, 'affiliation', $author->getAffiliation(), false);
		XMLCustomWriter::createChildWithText($doc, $root, 'country', $author->getCountry(), false);
		XMLCustomWriter::createChildWithText($doc, $root, 'email', $author->getEmail(), false);
		XMLCustomWriter::createChildWithText($doc, $root, 'url', $author->getUrl(), false);
		if (is_array($author->getBiography(null))) foreach ($author->getBiography(null) as $locale => $biography) {
			$biographyNode =& XMLCustomWriter::createChildWithText($doc, $root, 'biography', strip_tags($biography), false);
			if ($biographyNode) XMLCustomWriter::setAttribute($biographyNode, 'locale', $locale);
			unset($biographyNode);
		}

		return $root;
	}

	function &generateGalleyDom(&$doc, &$schedConf, &$paper, &$galley, $opts) {
		$isHtml = $galley->isHTMLGalley();

		import('file.PaperFileManager');
		$paperFileManager = new PaperFileManager($paper->getId());
		$paperFileDao =& DAORegistry::getDAO('PaperFileDAO');

		$root =& XMLCustomWriter::createElement($doc, $isHtml?'htmlgalley':'galley');
		if ($root) XMLCustomWriter::setAttribute($root, 'locale', $galley->getLocale());

		XMLCustomWriter::createChildWithText($doc, $root, 'label', $galley->getLabel());

		/* --- Galley file --- */
		$fileNode =& XMLCustomWriter::createElement($doc, 'file');
		XMLCustomWriter::appendChild($root, $fileNode);
		if (intval($galley->getFileId()) == 0) {
			error_log('The galley ID ' . $galley->getId() . ' is missing a file reference, skipping');
			return $root;
		}
		$paperFile =& $paperFileDao->getPaperFile($galley->getFileId());

		if (array_key_exists('no-embed', $opts)) {
			$galley->setData('type', 'public'); // otherwise missing penultimate part of file path
			$hrefNode =& XMLCustomWriter::createElement($doc, 'href');
			XMLCustomWriter::setAttribute($hrefNode, 'src', $galley->getFilePath());
			XMLCustomWriter::setAttribute($hrefNode, 'mime_type', $paperFile->getFileType());
			XMLCustomWriter::appendChild($fileNode, $hrefNode);
		} else {
			$embedNode =& XMLCustomWriter::createChildWithText($doc, $fileNode, 'embed', base64_encode($paperFileManager->readFile($galley->getFileId())));
			if (!$paperFile) return $paperFile; // Stupidity check
			XMLCustomWriter::setAttribute($embedNode, 'filename', $paperFile->getOriginalFileName());
			XMLCustomWriter::setAttribute($embedNode, 'encoding', 'base64');
			XMLCustomWriter::setAttribute($embedNode, 'mime_type', $paperFile->getFileType());
		}
		/* --- HTML-specific data: Stylesheet and/or images --- */

		if ($isHtml) {
			$styleFile = $galley->getStyleFile();
			if ($styleFile) {
				$styleNode =& XMLCustomWriter::createElement($doc, 'stylesheet');
				XMLCustomWriter::appendChild($root, $styleNode);
				$embedNode =& XMLCustomWriter::createChildWithText($doc, $styleNode, 'embed', base64_encode($paperFileManager->readFile($styleFile->getFileId())));
				XMLCustomWriter::setAttribute($embedNode, 'filename', $styleFile->getOriginalFileName());
				XMLCustomWriter::setAttribute($embedNode, 'encoding', 'base64');
				XMLCustomWriter::setAttribute($embedNode, 'mime_type', 'text/css');
			}

			foreach ($galley->getImageFiles() as $imageFile) {
				$imageNode =& XMLCustomWriter::createElement($doc, 'image');
				XMLCustomWriter::appendChild($root, $imageNode);
				$embedNode =& XMLCustomWriter::createChildWithText($doc, $imageNode, 'embed', base64_encode($paperFileManager->readFile($imageFile->getFileId())));
				XMLCustomWriter::setAttribute($embedNode, 'filename', $imageFile->getOriginalFileName());
				XMLCustomWriter::setAttribute($embedNode, 'encoding', 'base64');
				XMLCustomWriter::setAttribute($embedNode, 'mime_type', $imageFile->getFileType());
				unset($imageNode);
				unset($embedNode);
			}
		}

		return $root;
	}
	
	function &generateSuppFileDom(&$doc, &$schedConf, &$paper, &$suppFile) {
		$root =& XMLCustomWriter::createElement($doc, 'supplemental_file');

		// FIXME: These should be constants!
		switch ($suppFile->getType()) {
			case __('author.submit.suppFile.researchInstrument'):
				$suppFileType = 'research_instrument';
				break;
			case __('author.submit.suppFile.researchMaterials'):
				$suppFileType = 'research_materials';
				break;
			case __('author.submit.suppFile.researchResults'):
				$suppFileType = 'research_results';
				break;
			case __('author.submit.suppFile.transcripts'):
				$suppFileType = 'transcripts';
				break;
			case __('author.submit.suppFile.dataAnalysis'):
				$suppFileType = 'data_analysis';
				break;
			case __('author.submit.suppFile.dataSet'):
				$suppFileType = 'data_set';
				break;
			case __('author.submit.suppFile.sourceText'):
				$suppFileType = 'source_text';
				break;
			default:
				$suppFileType = 'other';
				break;
		}

		XMLCustomWriter::setAttribute($root, 'type', $suppFileType);
		XMLCustomWriter::setAttribute($root, 'public_id', $suppFile->getPublicSuppFileId(), false);
		XMLCustomWriter::setAttribute($root, 'language', $suppFile->getLanguage(), false);

		if (is_array($suppFile->getTitle(null))) foreach ($suppFile->getTitle(null) as $locale => $title) {
			$titleNode =& XMLCustomWriter::createChildWithText($doc, $root, 'title', $title, false);
			if ($titleNode) XMLCustomWriter::setAttribute($titleNode, 'locale', $locale);
			unset($titleNode);
		}
		if (is_array($suppFile->getCreator(null))) foreach ($suppFile->getCreator(null) as $locale => $creator) {
			$creatorNode =& XMLCustomWriter::createChildWithText($doc, $root, 'creator', $creator, false);
			if ($creatorNode) XMLCustomWriter::setAttribute($creatorNode, 'locale', $locale);
			unset($creatorNode);
		}
		if (is_array($suppFile->getSubject(null))) foreach ($suppFile->getSubject(null) as $locale => $subject) {
			$subjectNode =& XMLCustomWriter::createChildWithText($doc, $root, 'subject', $subject, false);
			if ($subjectNode) XMLCustomWriter::setAttribute($subjectNode, 'locale', $locale);
			unset($subjectNode);
		}
		if ($suppFileType == 'other') {
			if (is_array($suppFile->getTypeOther(null))) foreach ($suppFile->getTypeOther(null) as $locale => $typeOther) {
				$typeOtherNode =& XMLCustomWriter::createChildWithText($doc, $root, 'type_other', $typeOther, false);
				if ($typeOtherNode) XMLCustomWriter::setAttribute($typeOtherNode, 'locale', $locale);
				unset($typeOtherNode);
			}		
		}
		if (is_array($suppFile->getDescription(null))) foreach ($suppFile->getDescription(null) as $locale => $description) {
			$descriptionNode =& XMLCustomWriter::createChildWithText($doc, $root, 'description', $description, false);
			if ($descriptionNode) XMLCustomWriter::setAttribute($descriptionNode, 'locale', $locale);
			unset($descriptionNode);
		}
		if (is_array($suppFile->getPublisher(null))) foreach ($suppFile->getPublisher(null) as $locale => $publisher) {
			$publisherNode =& XMLCustomWriter::createChildWithText($doc, $root, 'publisher', $publisher, false);
			if ($publisherNode) XMLCustomWriter::setAttribute($publisherNode, 'locale', $locale);
			unset($publisherNode);
		}
		if (is_array($suppFile->getSponsor(null))) foreach ($suppFile->getSponsor(null) as $locale => $sponsor) {
			$sponsorNode =& XMLCustomWriter::createChildWithText($doc, $root, 'sponsor', $sponsor, false);
			if ($sponsorNode) XMLCustomWriter::setAttribute($sponsorNode, 'locale', $locale);
			unset($sponsorNode);
		}
		XMLCustomWriter::createChildWithText($doc, $root, 'date_created', NativeExportDom::formatDate($suppFile->getDateCreated()), false);
		if (is_array($suppFile->getSource(null))) foreach ($suppFile->getSource(null) as $locale => $source) {
			$sourceNode =& XMLCustomWriter::createChildWithText($doc, $root, 'source', $source, false);
			if ($sourceNode) XMLCustomWriter::setAttribute($sourceNode, 'locale', $locale);
			unset($sourceNode);
		}
		
		import('file.PaperFileManager');
		$paperFileManager = new PaperFileManager($paper->getId());
		$fileNode =& XMLCustomWriter::createElement($doc, 'file');
		XMLCustomWriter::appendChild($root, $fileNode);
		$embedNode =& XMLCustomWriter::createChildWithText($doc, $fileNode, 'embed', base64_encode($paperFileManager->readFile($suppFile->getFileId())));
		XMLCustomWriter::setAttribute($embedNode, 'filename', $suppFile->getOriginalFileName());
		XMLCustomWriter::setAttribute($embedNode, 'encoding', 'base64');
		XMLCustomWriter::setAttribute($embedNode, 'mime_type', $suppFile->getFileType());
		
		return $root;
	}

	function formatDate($date) {
		if ($date == '') return null;
		return date('Y-m-d', strtotime($date));
	}
}

?>
