<?php
Class PWA_Demo extends PHPHT_Model {
  protected $my_name = "PWA Demo";
  protected $view = "pwa.php";

  public function get($query = 'getAllQuery') {
    $result = array(
      [
        "animal" => "Platypus",
        "description" => "The platypus (Ornithorhynchus anatinus), sometimes referred to as the duck-billed platypus, is a semiaquatic egg-laying mammal[3] endemic to eastern Australia, including Tasmania. The platypus is the sole living representative of its family (Ornithorhynchidae) and genus (Ornithorhynchus), though a number of related species appear in the fossil record."
      ],
      [
        "animal" => "Koala",
        "description" => "The koala or, inaccurately, koala bear[a] (Phascolarctos cinereus) is an arboreal herbivorous marsupial native to Australia. It is the only extant representative of the family Phascolarctidae and its closest living relatives are the wombats, which comprise the family Vombatidae. The koala is found in coastal areas of the mainland's eastern and southern regions, inhabiting Queensland, New South Wales, Victoria, and South Australia. It is easily recognisable by its stout, tailless body and large head with round, fluffy ears and large, spoon-shaped nose. The koala has a body length of 60–85 cm (24–33 in) and weighs 4–15 kg (9–33 lb). Fur colour ranges from silver grey to chocolate brown. Koalas from the northern populations are typically smaller and lighter in colour than their counterparts further south. These populations possibly are separate subspecies, but this is disputed."
      ],
      [
        "animal" => "Tasmanian devil",
        "description" => "The Tasmanian devil (Sarcophilus harrisii) is a carnivorous marsupial of the family Dasyuridae. It was once native to mainland Australia and is now found in the wild only on the island state of Tasmania, including tiny east-coast Maria Island where there is a conservation project with disease-free animals."
      ],
      [
        "animal" => "Percheron",
        "description" => "The Percheron is a breed of draft horse that originated in the Huisne river valley in western France, part of the former Perche province from which the breed takes its name. Usually gray or black in color, Percherons are well muscled, and known for their intelligence and willingness to work. Although their exact origins are unknown, the ancestors of the breed were present in the valley by the 17th century. They were originally bred for use as war horses. Over time, they began to be used for pulling stagecoaches and later for agriculture and hauling heavy goods. In the late 18th and early 19th centuries, Arabian blood was added to the breed. Exports of Percherons from France to the United States and other countries rose exponentially in the late 19th century, and the first purely Percheron stud book was created in France in 1883."
      ],
      [
        "animal" => "Tardigrade",
        "description" => "Tardigrades (/ˈtɑːrdɪɡreɪd/), known colloquially as water bears or moss piglets,[1][2][3][4] are a phylum of water-dwelling eight-legged segmented micro-animals.[1][5] They were first described by the German zoologist Johann August Ephraim Goeze in 1773, who called them little water bears. In 1777, the Italian biologist Lazzaro Spallanzani named them Tardigrada, which means \"slow steppers\".[6]"
      ]
    );
    $returnList = array("result" => $result);
    return $returnList;
  }
}