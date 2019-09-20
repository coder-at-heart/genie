<?php

namespace Lnk7\Genie\Fields;

use Lnk7\Genie\Utilities\ConvertString;

class ColorSelectorField extends SelectField {

	protected $colors = [
		'Vitol Blue'   => '#002B54',
		'Vitol Orange' => '#F3901D',
		'Sky Red'      => '#D50032',
		'Warm Red'     => '#F9423A',
		'Yellow'       => '#FFC72C',
		'Forest Green' => '#004C45',
		'Green'        => '#007367',
		'Turquoise'    => '#00A499',
		'Sky Blue'     => '#0033A0',
		'Purple'       => '#772583',
		'Magenta'      => '#C6007E',
		'Grey'         => '#63666A',
		'Warm Grey'    => '#968C83',
		'Silver'       => '#8A8D8F',
		'White'        => '#FFF',
		'Black'        => '#000',
	];



	public function setChoices() {

		$choices = [];
		foreach ( $this->colors as $name => $hex ) {

			$colorCode = ConvertString::From( $name )->toCamelCase();

			$choices[ $colorCode ] = '<i class="fa fa-circle" style="font-size:20px; color:' . $hex . '"></i> ' . $name . ' </div>';
		}

		$this->choices( $choices );
	}



	protected function setDefaults() {

		parent::setDefaults();
		$this->returnFormat( 'value' );
		$this->setChoices();
	}

}