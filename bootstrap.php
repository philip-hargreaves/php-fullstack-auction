<?php

use app\repositories\AuctionImageRepository;
use app\repositories\UserRepository;
use app\repositories\RoleRepository;
use app\repositories\ItemRepository;
use app\repositories\AuctionRepository;
use app\repositories\BidRepository;
use app\repositories\UserRoleRepository;
use app\repositories\WatchlistRepository;
use app\repositories\CategoryRepository;
use app\services\BidService;
use app\services\AuthService;
use app\services\AuctionService;
use app\services\UserService;
use app\services\WatchlistService;
use app\services\ImageService;
use app\services\CategoryService;
use app\services\RecommendationService;
use infrastructure\Database;
use infrastructure\DIContainer;
use app\services\RoleService;
use app\services\ItemService;

// --- Build all objects and bind them to the App Container ---
// Bind the Database first (it has no dependencies)
DIContainer::bind('db', new Database());

// Bind Repositories (they depend on the db)
DIContainer::bind('roleRepo', new RoleRepository(
    DIContainer::get('db')));

DIContainer::bind('userRepo', new UserRepository(
    DIContainer::get('db'),
    DIContainer::get('roleRepo')));

DIContainer::bind('userRoleRepo', new UserRoleRepository(
    DIContainer::get('db')));

DIContainer::bind('itemRepo', new ItemRepository(
    DIContainer::get('db'),
    DIContainer::get('userRepo')));

DIContainer::bind('watchlistRepo', new WatchlistRepository(
    DIContainer::get('db')));

DIContainer::bind('auctionImageRepo', new AuctionImageRepository(
    DIContainer::get('db')
));

DIContainer::bind('auctionRepo', new AuctionRepository(
    DIContainer::get('db'),
    DIContainer::get('itemRepo'),
    DIContainer::get('auctionImageRepo'),));

DIContainer::bind('bidRepo', new BidRepository(
    DIContainer::get('db'),
    DIContainer::get('userRepo'),
    DIContainer::get('auctionRepo')));

DIContainer::bind('categoryRepo', new CategoryRepository(
    DIContainer::get('db')
));

DIContainer::bind('categoryServ', new CategoryService(
    DIContainer::get('categoryRepo')
));

DIContainer::bind('itemServ', new ItemService(
    DIContainer::get('itemRepo'),
    DIContainer::get('userRepo')
));

DIContainer::bind('imageServ', new ImageService(
    DIContainer::get('auctionImageRepo'),
    DIContainer::get('auctionRepo'),
    DIContainer::get('db')));

// Bind Services (they depend on repositories)
DIContainer::bind('bidServ', new BidService(
    DIContainer::get('bidRepo'),
    DIContainer::get('auctionRepo'),
    DIContainer::get('userRepo'),
    DIContainer::get('db')));

DIContainer::bind('authServ', new AuthService(
    DIContainer::get('userRepo')));

DIContainer::bind('userServ', new UserService(
    DIContainer::get('userRepo'),
    DIContainer::get('userRoleRepo'),
    DIContainer::get('roleRepo'),
    DIContainer::get('db')));

DIContainer::bind('roleServ', new RoleService(
    DIContainer::get('userRepo'),
    DIContainer::get('roleRepo'),
    DIContainer::get('userRoleRepo'),
    DIContainer::get('db')));

DIContainer::bind('auctionServ', new AuctionService(
    DIContainer::get('db'),
    DIContainer::get('auctionRepo'),
    DIContainer::get('itemRepo'),
    DIContainer::get('itemServ'),
    DIContainer::get('imageServ'),
    DIContainer::get('bidServ'),
    DIContainer::get('categoryRepo'),
    DIContainer::get('auctionImageRepo')));

DIContainer::bind('watchlistServ', new WatchlistService(
    DIContainer::get('watchlistRepo'),
    DIContainer::get('auctionServ')));

DIContainer::bind('recommendationServ', new RecommendationService(
    DIContainer::get('auctionRepo')));