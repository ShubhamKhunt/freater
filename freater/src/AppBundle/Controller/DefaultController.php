<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

// Include the requires classes of Phpword
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Style\Image;
use PhpOffice\PhpWord\Shared\Converter;
use PhpOffice\PhpWord\SimpleType\Jc;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $phpWord = new PhpWord();

        //$imagesFolder = $this->get('kernel')->getRootDir() . '/../source/images/';
        //$_local_image_path = $imagesFolder.'visa.png';
        $_local_image_path = "https://i.ytimg.com/vi/PCwL3-hkKrg/maxresdefault.jpg";

        // Begin code
        $section = $phpWord->addSection();
        $section->addText('Local image without any styles:');
        $section->addImage($_local_image_path);
        $section->addTextBreak(2);
        $section->addText('Local image with styles:');
        $section->addImage($_local_image_path, array('width' => 210, 'height' => 210, 'alignment' => Jc::CENTER));
        $section->addTextBreak(2);
        
        $source = 'http://res.cloudinary.com/demo/image/upload/v1362144095/sample_attachment.jpg';
        $section->addText("Remote image from: {$source}");
        $section->addImage($source);

        // Wrapping style
        $text = str_repeat('Hello World! ', 15);
        $wrappingStyles = array('inline', 'behind', 'infront', 'square', 'tight');
        foreach ($wrappingStyles as $wrappingStyle) {
            $section->addTextBreak(5);
            $section->addText("Wrapping style {$wrappingStyle}");
            $section->addImage(
                $_local_image_path,
                array(
                    'positioning'   => 'relative',
                    'marginTop'     => -1,
                    'marginLeft'    => 1,
                    'width'         => 80,
                    'height'        => 80,
                    'wrappingStyle' => $wrappingStyle,
                )
            );
            $section->addText($text);
        }

        //Absolute positioning
        $section->addTextBreak(3);
        $section->addText('Absolute positioning: see top right corner of page');
        $section->addImage(
            $_local_image_path,
            array(
                'width'            => Converter::cmToPixel(3),
                'height'           => Converter::cmToPixel(3),
                'positioning'      => Image::POSITION_ABSOLUTE,
                'posHorizontal'    => Image::POSITION_HORIZONTAL_RIGHT,
                'posHorizontalRel' => Image::POSITION_RELATIVE_TO_PAGE,
                'posVerticalRel'   => Image::POSITION_RELATIVE_TO_PAGE,
                'marginLeft'       => Converter::cmToPixel(15.5),
                'marginTop'        => Converter::cmToPixel(1.55),
            )
        );

        //Relative positioning
        $section->addTextBreak(3);
        $section->addText('Relative positioning: Horizontal position center relative to column,');
        $section->addText('Vertical position top relative to line');
        $section->addImage(
            $_local_image_path,
            array(
                'width'            => Converter::cmToPixel(3),
                'height'           => Converter::cmToPixel(3),
                'positioning'      => Image::POSITION_RELATIVE,
                'posHorizontal'    => Image::POSITION_HORIZONTAL_CENTER,
                'posHorizontalRel' => Image::POSITION_RELATIVE_TO_COLUMN,
                'posVertical'      => Image::POSITION_VERTICAL_TOP,
                'posVerticalRel'   => Image::POSITION_RELATIVE_TO_LINE,
            )
        );

        // Saving the document as OOXML file...
        $objWriter = IOFactory::createWriter($phpWord, 'Word2007');

        $filePath = "source/reports/sample.docx";
        // Write file into path
        $objWriter->save($filePath);

        exit('--end--');

        /* return $this->render('default/index.html.twig', [
            'base_dir' => realpath($this->getParameter('kernel.project_dir')).DIRECTORY_SEPARATOR,
        ]); */
    }
}
