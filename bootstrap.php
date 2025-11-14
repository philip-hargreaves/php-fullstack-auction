<?php

use app\repositories\UserRepository;
use app\repositories\RoleRepository;
use app\repositories\ItemRepository;
use app\repositories\AuctionRepository;
use app\repositories\BidRepository;
use app\repositories\UserRoleRepository;
use app\services\BidService;
use app\services\AuthService;
use app\services\RegistrationService;
use infrastructure\Database;
use infrastructure\DIContainer;

// --- Build all objects and bind them to the App Container ---
// Bind the Database first (it has no dependencies)
DIContainer::bind('db', new Database());

// Bind Repositories (they depend on the db)
DIContainer::bind('roleRepo', new RoleRepository(DIContainer::get('db')));
DIContainer::bind('userRepo', new UserRepository(DIContainer::get('db'), DIContainer::get('roleRepo')));
DIContainer::bind('userRoleRepo', new UserRoleRepository(DIContainer::get('db')));
DIContainer::bind('itemRepo', new ItemRepository(DIContainer::get('db'), DIContainer::get('userRepo')));
DIContainer::bind('auctionRepo', new AuctionRepository(DIContainer::get('db'), DIContainer::get('itemRepo')));
DIContainer::bind('bidRepo', new BidRepository(DIContainer::get('db'), DIContainer::get('userRepo'), DIContainer::get('auctionRepo')));

// Bind Services (they depend on repositories)
DIContainer::bind('bidServ', new BidService(DIContainer::get('bidRepo'), DIContainer::get('auctionRepo'), DIContainer::get('db')));
DIContainer::bind('authServ', new AuthService(DIContainer::get('userRepo')));
DIContainer::bind('registrationServ', new RegistrationService(DIContainer::get('userRepo'), DIContainer::get('userRoleRepo'), DIContainer::get('roleRepo'), DIContainer::get('db'),));