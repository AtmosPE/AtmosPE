<?php
namespace pocketmine\level\generator\biome;
use pocketmine\block\Block;
use pocketmine\level\ChunkManager;
use pocketmine\level\generator\normal\biome\SwampBiome;
use pocketmine\level\generator\normal\biome\BeachBiome;
use pocketmine\level\generator\normal\biome\DesertBiome;
use pocketmine\level\generator\normal\biome\ForestBiome;
use pocketmine\level\generator\normal\biome\RoofedForestBiome;
use pocketmine\level\generator\normal\biome\SavannaBiome;
use pocketmine\level\generator\normal\biome\IcePlainsBiome;
use pocketmine\level\generator\normal\biome\MushroomIslandBiome;
use pocketmine\level\generator\normal\biome\MountainsBiome;
use pocketmine\level\generator\normal\biome\OceanBiome;
use pocketmine\level\generator\normal\biome\PlainBiome;
use pocketmine\level\generator\normal\biome\RiverBiome;
use pocketmine\level\generator\normal\biome\FrozenRiverBiome;
use pocketmine\level\generator\normal\biome\SmallMountainsBiome;
use pocketmine\level\generator\normal\biome\JungleBiome;
use pocketmine\level\generator\normal\biome\MesaBiome;
use pocketmine\level\generator\normal\biome\TaigaBiome;
use pocketmine\level\generator\hell\HellBiome;
use pocketmine\level\generator\populator\Populator;
use pocketmine\utils\Random;
use pocketmine\level\generator\populator\Flower;

abstract class Biome{
	const OCEAN = 0;
	const PLAINS = 1;
	const DESERT = 2;
	const MOUNTAINS = 3;
	const FOREST = 4;
	const TAIGA = 5;
	const SWAMP = 6;
	const RIVER = 7;
	const HELL = 8;
	const END = 9;
	const FROZEN_RIVER = 11;
	const ICE_PLAINS = 12;
	const ICE_MOUNTAINS = 13;
	const MUSHROOM_ISLAND = 14;
	const MUSHROOM_ISLAND_SHORE = 15;
	const BEACH = 16;
	const DESERT_HILLS = 17;
	const FOREST_HILLS = 18;
	const TAIGA_HILLS = 19;
	const SMALL_MOUNTAINS = 20;
	const JUNGLE = 21;
	const JUNGLE_HILLS = 22;
	const JUNGLE_EDGE = 23;
	const DEEP_OCEAN = 24;
	const STONE_BEACH = 25;
	const COLD_BEACH = 26;
	const BIRCH_FOREST = 27;
	const BIRCH_FOREST_HILLS = 28;
	const ROOFED_FOREST = 29;
	const COLD_TAIGA = 30;
	const COLD_TAIGA_HILLS = 31;
	const MEGA_TAIGA = 32;
	const MEGA_TAIGA_HILLS = 33;
	const EXTREME_HILLS_PLUS = 34;
	const SAVANNA = 35;
	const SAVANNA_PLATEAU = 36;
	const MESA = 37;
	const MESA_PLATEAU_F = 38;
	const MESA_PLATEAU = 39;
	const VOID = 127;
	//const MAX_BIOMES = 256; idk is here this. If is implemented FarLand need, but is not implemented. Fix far land in multiplayer
	
	/*
    SWAMPLAND,-> added (6)
    FOREST, -> added (4)
    TAIGA, -> added (5)
    DESERT, -> added (2)
    PLAINS, -> added (1)
    HELL, -> added (8)
    SKY, -> idk is this.. :/
    OCEAN, -> added (0)
    RIVER, -> added (7)
    EXTREME_HILLS, -> in construction (34)
    FROZEN_OCEAN, -> not exit in mcpe
    FROZEN_RIVER, -> in construction (11)
    ICE_PLAINS,
    ICE_MOUNTAINS,
    MUSHROOM_ISLAND,
    MUSHROOM_SHORE,
    BEACH,
    DESERT_HILLS,
    FOREST_HILLS,
    TAIGA_HILLS,
    SMALL_MOUNTAINS,
    JUNGLE,
    JUNGLE_HILLS,
    JUNGLE_EDGE,
    DEEP_OCEAN,
    STONE_BEACH,
    COLD_BEACH,
    BIRCH_FOREST,
    BIRCH_FOREST_HILLS,
    ROOFED_FOREST,
    COLD_TAIGA,
    COLD_TAIGA_HILLS,
    MEGA_TAIGA,
    MEGA_TAIGA_HILLS,
    EXTREME_HILLS_PLUS,
    SAVANNA,
    SAVANNA_PLATEAU,
    MESA, -> in construction (37)
    MESA_PLATEAU_FOREST,
    MESA_PLATEAU,
    SUNFLOWER_PLAINS,
    DESERT_MOUNTAINS,
    FLOWER_FOREST,
    TAIGA_MOUNTAINS,
    SWAMPLAND_MOUNTAINS,
    ICE_PLAINS_SPIKES,
    JUNGLE_MOUNTAINS,
    JUNGLE_EDGE_MOUNTAINS,
    COLD_TAIGA_MOUNTAINS,
    SAVANNA_MOUNTAINS,
    SAVANNA_PLATEAU_MOUNTAINS,
    MESA_BRYCE,
    MESA_PLATEAU_FOREST_MOUNTAINS,
    MESA_PLATEAU_MOUNTAINS,
    BIRCH_FOREST_MOUNTAINS,
    BIRCH_FOREST_HILLS_MOUNTAINS,
    ROOFED_FOREST_MOUNTAINS,
    MEGA_SPRUCE_TAIGA,
    EXTREME_HILLS_MOUNTAINS,
    EXTREME_HILLS_PLUS_MOUNTAINS,
    MEGA_SPRUCE_TAIGA_HILLS,
    */

	/** @var Biome[] */
	private static $biomes = [];

	private $id;
	private $registered = false;
	/** @var Populator[] */
	private $populators = [];

	private $minElevation;
	private $maxElevation;

	private $groundCover = [];

	protected $rainfall = 0.5;
	protected $temperature = 0.5;
	protected $grassColor = 0;

	protected static function register($id, Biome $biome){
		self::$biomes[(int) $id] = $biome;
		$biome->setId((int) $id);
		$biome->grassColor = self::generateBiomeColor($biome->getTemperature(), $biome->getRainfall());

		$flowerPopFound = false;

		foreach($biome->getPopulators() as $populator){
			if($populator instanceof Flower){
				$flowerPopFound = true;
				break;
			}
		}

		if($flowerPopFound === false){
			$flower = new Flower();
			$biome->addPopulator($flower);
		}
	}
/*
Mountains and Small Mountains is not principal biomes. Fix in future
*/
	public static function init(){
		self::register(self::OCEAN, new OceanBiome());
		self::register(self::PLAINS, new PlainBiome());
		self::register(self::DESERT, new DesertBiome());
		self::register(self::MOUNTAINS, new MountainsBiome()); 
		self::register(self::FOREST, new ForestBiome());
		self::register(self::ROOFED_FOREST, new RoofedForestBiome());
		self::register(self::SAVANNA, new SavannaBiome());
		self::register(self::TAIGA, new TaigaBiome());
		self::register(self::SWAMP, new SwampBiome());
		self::register(self::RIVER, new RiverBiome());
		self::register(self::FROZEN_RIVER, new FrozenRiverBiome());
		self::register(self::ICE_PLAINS, new IcePlainsBiome());
		self::register(self::MUSHROOM_ISLAND, new MushroomIslandBiome());
		self::register(self::BEACH, new BeachBiome());
		self::register(self::SMALL_MOUNTAINS, new SmallMountainsBiome()); 
		self::register(self::JUNGLE, new JungleBiome());
		self::register(self::MESA, new MesaBiome());
		self::register(self::HELL, new HellBiome());
		self::register(self::BIRCH_FOREST, new ForestBiome(ForestBiome::TYPE_BIRCH));
	}

	/**
	 * @param $id
	 *
	 * @return Biome
	 */
	public static function getBiome($id){
		return isset(self::$biomes[$id]) ? self::$biomes[$id] : self::$biomes[self::OCEAN];
	}

	public function clearPopulators(){
		$this->populators = [];
	}

	public function addPopulator(Populator $populator){
		$this->populators[get_class($populator)] = $populator;
	}

	public function removePopulator($class){
		if(isset($this->populators[$class])){
			unset($this->populators[$class]);
		}
	}

	public function populateChunk(ChunkManager $level, $chunkX, $chunkZ, Random $random){
		foreach($this->populators as $populator){
			$populator->populate($level, $chunkX, $chunkZ, $random);
		}
	}

	public function getPopulators(){
		return $this->populators;
	}

	public function setId($id){
		if(!$this->registered){
			$this->registered = true;
			$this->id = $id;
		}
	}

	public function getId(){
		return $this->id;
	}

	public abstract function getName();

	public function getMinElevation(){
		return $this->minElevation;
	}

	public function getMaxElevation(){
		return $this->maxElevation;
	}

	public function setElevation($min, $max){
		$this->minElevation = $min;
		$this->maxElevation = $max;
	}

	/**
	 * @return Block[]
	 */
	public function getGroundCover(){
		return $this->groundCover;
	}

	/**
	 * @param Block[] $covers
	 */
	public function setGroundCover(array $covers){
		$this->groundCover = $covers;
	}

	public function getTemperature(){
		return $this->temperature;
	}

	public function getRainfall(){
		return $this->rainfall;
	}

	private static function generateBiomeColor($temperature, $rainfall){
		$x = (1 - $temperature) * 255;
		$z = (1 - $rainfall * $temperature) * 255;
		$c = self::interpolateColor(256, $x, $z, [0x47, 0xd0, 0x33], [0x6c, 0xb4, 0x93], [0xbf, 0xb6, 0x55], [0x80, 0xb4, 0x97]);
		return ((int) ($c[0] << 16)) | (int) (($c[1] << 8)) | (int) ($c[2]);
	}


	private static function interpolateColor($size, $x, $z, $c1, $c2, $c3, $c4){
		$l1 = self::lerpColor($c1, $c2, $x / $size);
		$l2 = self::lerpColor($c3, $c4, $x / $size);

		return self::lerpColor($l1, $l2, $z / $size);
	}

	private static function lerpColor($a, $b, $s){
		$invs = 1 - $s;
		return [$a[0] * $invs + $b[0] * $s, $a[1] * $invs + $b[1] * $s, $a[2] * $invs + $b[2] * $s];
	}


	/**
	 * @return int (Red|Green|Blue)
	 */
	abstract public function getColor();
}
